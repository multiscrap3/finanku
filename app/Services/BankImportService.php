<?php

namespace App\Services;

use App\Models\ImportBank;
use App\Models\Kategori;
use App\Models\SumberTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\BankParsers\BCAParser;
use App\Services\BankParsers\BNIParser;
use App\Services\BankParsers\BSIParser;
use App\Services\BankParsers\GenericParser;
use App\Services\BankParsers\MandiriExcelParser;
use App\Services\BankParsers\MandiriParser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class BankImportService
{
    public function import(User $user, UploadedFile $file, int $sumberTransaksiId, string $bankCode = 'generic', ?int $kategoriId = null, ?string $password = null): ImportBank
    {
        $sumberTransaksi = SumberTransaksi::query()
            ->where('household_id', $user->household_id)
            ->findOrFail($sumberTransaksiId);

        $kategoriId ??= $this->resolveDefaultKategoriId($user);

        $ext  = strtolower($file->getClientOriginalExtension()) ?: 'csv';
        $path = $file->storeAs('imports/bank', Str::uuid() . '.' . $ext);

        $import = ImportBank::create([
            'household_id' => $user->household_id,
            'user_id' => $user->id,
            'sumber_transaksi_id' => $sumberTransaksi->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'total_rows' => 0,
            'imported_rows' => 0,
            'failed_rows' => 0,
            'errors' => null,
            'status' => 'pending',
        ]);

        $import->markProcessing();

        try {
            $parser = $this->resolveParser($bankCode, Storage::path($path));
            $rows = $parser->parse(Storage::path($path), ['password' => $password]);

            [$importedRows, $failedRows, $errors] = DB::transaction(function () use ($rows, $user, $sumberTransaksi, $kategoriId) {
                $importedRows = 0;
                $failedRows = 0;
                $errors = [];

                foreach ($rows as $index => $row) {
                    try {
                        Transaksi::create([
                            'household_id' => $user->household_id,
                            'user_id' => $user->id,
                            'kategori_id' => $kategoriId,
                            'sumber_transaksi_id' => $sumberTransaksi->id,
                            'jenis' => $row['jenis'],
                            'jumlah' => $row['jumlah'],
                            'tanggal' => $row['tanggal'],
                            'keterangan' => $row['keterangan'],
                            'bukti_transaksi' => null,
                            'transfer_ke_id' => null,
                            'is_recurring' => false,
                            'recurring_id' => null,
                        ]);

                        $importedRows++;
                    } catch (Throwable $exception) {
                        $failedRows++;
                        $errors[] = [
                            'row' => $index + 2,
                            'message' => $exception->getMessage(),
                            'data' => $row,
                        ];
                    }
                }

                return [$importedRows, $failedRows, $errors];
            });

            $import->markCompleted(count($rows), $importedRows, $failedRows, $this->maskErrors($errors));

            return $import->fresh(['sumberTransaksi', 'user']);
        } catch (Throwable $exception) {
            $import->markFailed([
                ['message' => $exception->getMessage()],
            ]);

            return $import->fresh(['sumberTransaksi', 'user']);
        } finally {
            // PDP: hapus file segera setelah diproses — tidak perlu disimpan permanen
            Storage::delete($path);
            $import->update(['file_path' => null]);
        }
    }

    public function preview(UploadedFile $file, string $bankCode = 'generic', ?string $password = null): array
    {
        $ext  = strtolower($file->getClientOriginalExtension()) ?: 'csv';
        $path = $file->storeAs('imports/bank/previews', Str::uuid() . '.' . $ext);

        try {
            $parser = $this->resolveParser($bankCode, Storage::path($path));
            $rows = $parser->parse(Storage::path($path), ['password' => $password]);

            return [
                'rows' => array_slice($rows, 0, 20),
                'total' => count($rows),
                'file_path' => $path,
            ];
        } finally {
            Storage::delete($path);
        }
    }

    protected function resolveParser(string $bankCode, string $filePath): object
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $excelParsers = [
                new MandiriExcelParser(),
            ];

            foreach ($excelParsers as $parser) {
                if ($parser->supports($bankCode)) {
                    return $parser;
                }
            }

            throw new InvalidArgumentException("Parser Excel untuk bank {$bankCode} belum tersedia. Gunakan format CSV.");
        }

        $parsers = [
            new BCAParser(),
            new MandiriParser(),
            new BNIParser(),
            new BSIParser(),
            new GenericParser(),
        ];

        foreach ($parsers as $parser) {
            if ($parser->supports($bankCode)) {
                return $parser;
            }
        }

        throw new InvalidArgumentException("Parser bank {$bankCode} tidak tersedia.");
    }

    /**
     * Masking data sensitif di error log sesuai UU PDP.
     * Keterangan dipotong dan nominal disembunyikan sebelum disimpan ke DB.
     */
    protected function maskErrors(array $errors): array
    {
        return array_map(function (array $err) {
            if (isset($err['data']) && is_array($err['data'])) {
                $data = $err['data'];

                if (isset($data['keterangan'])) {
                    $ket = (string) $data['keterangan'];
                    $data['keterangan'] = mb_strlen($ket) > 4
                        ? mb_substr($ket, 0, 4) . str_repeat('*', min(8, mb_strlen($ket) - 4))
                        : '****';
                }

                foreach (['jumlah', 'debit', 'kredit', 'saldo'] as $field) {
                    if (isset($data[$field])) {
                        $data[$field] = '***';
                    }
                }

                $err['data'] = $data;
            }

            return $err;
        }, $errors);
    }

    protected function resolveDefaultKategoriId(User $user): ?int
    {
        return Kategori::query()
            ->where('household_id', $user->household_id)
            ->where('jenis', 'pengeluaran')
            ->orderBy('id')
            ->value('id');
    }
}