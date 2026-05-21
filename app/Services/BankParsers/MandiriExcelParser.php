<?php

namespace App\Services\BankParsers;

use App\Exceptions\ExcelPasswordRequiredException;
use App\Services\ExcelDecryptService;
use Carbon\Carbon;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MandiriExcelParser implements BankParserInterface
{
    public function supports(string $bankCode): bool
    {
        return strtolower($bankCode) === 'mandiri';
    }

    public function parse(string $filePath, array $options = []): array
    {
        if (! is_readable($filePath)) {
            throw new InvalidArgumentException('File Excel tidak dapat dibaca.');
        }

        $password   = $options['password'] ?? null;
        $decryptor  = new ExcelDecryptService();
        $tempFile   = null;

        try {
            // If file is encrypted, decrypt it first using Python msoffcrypto
            if ($decryptor->isEncrypted($filePath)) {
                if (! $password) {
                    throw new ExcelPasswordRequiredException();
                }
                // decrypt() throws InvalidArgumentException on wrong password
                $tempFile = $decryptor->decrypt($filePath, $password);
                $fileToRead = $tempFile;
            } else {
                $fileToRead = $filePath;
            }

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fileToRead);
        } catch (ExcelPasswordRequiredException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new InvalidArgumentException('Gagal membuka file Excel: ' . $e->getMessage());
        } finally {
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $headerRowIndex = $this->findHeaderRow($sheet, $highestRow);
        if ($headerRowIndex === null) {
            throw new InvalidArgumentException('Format file Excel Mandiri tidak dikenali. Pastikan file adalah mutasi rekening Bank Mandiri.');
        }

        $columns = $this->mapColumns($sheet, $headerRowIndex);

        // Skip header row + sub-header row (Mandiri has 2 header rows)
        $dataStartRow = $headerRowIndex + 2;

        return $this->parseRows($sheet, $dataStartRow, $highestRow, $columns);
    }

    protected function findHeaderRow(Worksheet $sheet, int $maxRow): ?int
    {
        $keywords = ['tanggal', 'date', 'transaction date', 'posting date'];

        for ($row = 1; $row <= min($maxRow, 40); $row++) {
            $rowData = $sheet->getRowIterator($row, $row)->current();
            if ($rowData === null) {
                continue;
            }
            foreach ($rowData->getCellIterator() as $cell) {
                $value = strtolower(trim((string) $cell->getValue()));
                foreach ($keywords as $keyword) {
                    if (str_contains($value, $keyword)) {
                        return $row;
                    }
                }
            }
        }

        return null;
    }

    protected function mapColumns(Worksheet $sheet, int $headerRow): array
    {
        $columns = [
            'tanggal'    => null,
            'keterangan' => null,
            'dana_masuk' => null,
            'dana_keluar' => null,
            'saldo'      => null,
        ];

        $mappings = [
            'tanggal'    => ['tanggal', 'date', 'transaction date', 'posting date'],
            'keterangan' => ['keterangan', 'remarks', 'remark', 'description', 'uraian'],
            'dana_masuk' => ['dana masuk', 'incoming', 'credit', 'kredit', 'masuk', 'mutasi kredit'],
            'dana_keluar' => ['dana keluar', 'outgoing', 'debit', 'keluar', 'mutasi debit', 'withdrawal'],
            'saldo'      => ['saldo', 'balance', 'running balance'],
        ];

        $rowData = $sheet->getRowIterator($headerRow, $headerRow)->current();
        if ($rowData === null) {
            throw new InvalidArgumentException('Baris header tidak ditemukan dalam file Excel.');
        }

        foreach ($rowData->getCellIterator() as $cell) {
            $value = strtolower(trim((string) $cell->getValue()));
            if ($value === '') {
                continue;
            }

            foreach ($mappings as $key => $keywords) {
                if ($columns[$key] !== null) {
                    continue;
                }
                foreach ($keywords as $keyword) {
                    if (str_contains($value, $keyword)) {
                        $columns[$key] = $cell->getColumn();
                        break;
                    }
                }
            }
        }

        if ($columns['tanggal'] === null || $columns['keterangan'] === null) {
            throw new InvalidArgumentException('Kolom tanggal atau keterangan tidak ditemukan dalam file Excel Mandiri.');
        }

        if ($columns['dana_masuk'] === null && $columns['dana_keluar'] === null) {
            throw new InvalidArgumentException('Kolom nominal transaksi (Dana Masuk / Dana Keluar) tidak ditemukan dalam file Excel Mandiri.');
        }

        return $columns;
    }

    protected function parseRows(Worksheet $sheet, int $startRow, int $endRow, array $columns): array
    {
        $results = [];
        $row = $startRow;

        while ($row <= $endRow) {
            $tanggal    = trim((string) $sheet->getCell($columns['tanggal'] . $row)->getValue());
            $keterangan = trim((string) $sheet->getCell($columns['keterangan'] . $row)->getValue());

            if ($tanggal === '' && $keterangan === '') {
                $row++;
                continue;
            }

            // Only process rows whose date column looks like a real date (not a time-only value)
            if ($tanggal === '' || $this->isTimeOnly($tanggal)) {
                $row++;
                continue;
            }

            $nextRow = $row + 1;
            $nextTanggal    = $nextRow <= $endRow ? trim((string) $sheet->getCell($columns['tanggal'] . $nextRow)->getValue()) : '';
            $nextKeterangan = $nextRow <= $endRow ? trim((string) $sheet->getCell($columns['keterangan'] . $nextRow)->getValue()) : '';

            // Combine date + time from next row if it contains a time value
            $fullDate = $tanggal;
            $skipNext = false;
            if ($nextTanggal !== '' && $this->isTimeOnly($nextTanggal)) {
                $fullDate = $tanggal . ' ' . $nextTanggal;
                $skipNext = true;
            }

            // Combine description lines
            $fullKeterangan = $keterangan;
            if ($skipNext && $nextKeterangan !== '') {
                $fullKeterangan = trim($keterangan . ' ' . $nextKeterangan);
            }

            $kredit = $columns['dana_masuk']  ? $this->parseMoney((string) $sheet->getCell($columns['dana_masuk'] . $row)->getCalculatedValue())  : 0.0;
            $debit  = $columns['dana_keluar'] ? $this->parseMoney((string) $sheet->getCell($columns['dana_keluar'] . $row)->getCalculatedValue()) : 0.0;
            $saldo  = $columns['saldo']       ? $this->parseMoney((string) $sheet->getCell($columns['saldo'] . $row)->getCalculatedValue())       : 0.0;

            if ($kredit <= 0 && $debit <= 0) {
                $row += $skipNext ? 2 : 1;
                continue;
            }

            $jenis  = $kredit >= $debit ? 'pemasukan' : 'pengeluaran';
            $jumlah = max($kredit, $debit);

            try {
                $results[] = [
                    'tanggal'    => $this->parseDate($fullDate, $row),
                    'keterangan' => $fullKeterangan ?: 'Import mutasi Mandiri',
                    'debit'      => $debit,
                    'kredit'     => $kredit,
                    'jumlah'     => $jumlah,
                    'jenis'      => $jenis,
                    'saldo'      => $saldo,
                    'raw'        => [],
                ];
            } catch (\Throwable) {
                // skip rows with unparseable dates
            }

            $row += $skipNext ? 2 : 1;
        }

        return $results;
    }

    protected function isTimeOnly(string $value): bool
    {
        return (bool) preg_match('/^\d{1,2}:\d{2}(:\d{2})?(\s*(WIB|WITA|WIT))?$/i', $value);
    }

    protected function parseMoney(string $value): float
    {
        if (trim($value) === '') {
            return 0.0;
        }

        $normalized = str_replace(['Rp', 'IDR', 'idr', ' '], '', $value);
        $normalized = preg_replace('/[^0-9,.-]/', '', $normalized) ?? '0';

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $lastComma = strrpos($normalized, ',');
            $lastDot   = strrpos($normalized, '.');

            if ($lastComma > $lastDot) {
                // Indonesian format: 1.234.567,89
                $normalized = str_replace('.', '', $normalized);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                // English format: 1,234,567.89
                $normalized = str_replace(',', '', $normalized);
            }
        } else {
            $normalized = str_replace(',', '.', $normalized);
        }

        return abs((float) $normalized);
    }

    protected function parseDate(string $value, int $lineNumber): string
    {
        // Strip time portion if present (e.g. "01 May 2026 07:28:47 WIB")
        $dateOnly = preg_replace('/\s+\d{1,2}:\d{2}(:\d{2})?(\s*(WIB|WITA|WIT))?$/i', '', trim($value));
        $dateOnly = trim($dateOnly ?? $value);

        $formats = ['d M Y', 'd F Y', 'Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'Y/m/d'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateOnly)->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($dateOnly)->format('Y-m-d');
        } catch (\Throwable) {
            throw new InvalidArgumentException("Format tanggal pada baris {$lineNumber} tidak valid: {$dateOnly}");
        }
    }
}
