<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\Transaksi;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnggaranService
{
    /**
     * Create anggaran baru
     */
    public function create(array $data): Anggaran
    {
        return Anggaran::create([
            'household_id' => auth()->user()->household_id,
            'kategori_id' => $data['kategori_id'],
            'bulan' => $data['bulan'],
            'tahun' => $data['tahun'],
            'jumlah' => $data['jumlah'],
            'terpakai' => 0,
        ]);
    }

    /**
     * Update anggaran
     */
    public function update(Anggaran $anggaran, array $data): Anggaran
    {
        $anggaran->update([
            'kategori_id' => $data['kategori_id'] ?? $anggaran->kategori_id,
            'bulan' => $data['bulan'] ?? $anggaran->bulan,
            'tahun' => $data['tahun'] ?? $anggaran->tahun,
            'jumlah' => $data['jumlah'] ?? $anggaran->jumlah,
        ]);

        return $anggaran->fresh();
    }

    /**
     * Delete anggaran
     */
    public function delete(Anggaran $anggaran): bool
    {
        return $anggaran->delete();
    }

    /**
     * Get realisasi anggaran
     */
    public function getRealisasi(Anggaran $anggaran): array
    {
        $persentase = $anggaran->jumlah > 0 
            ? ($anggaran->terpakai / $anggaran->jumlah) * 100 
            : 0;

        $sisa = $anggaran->jumlah - $anggaran->terpakai;
        $status = $this->getStatus($persentase);

        return [
            'jumlah' => $anggaran->jumlah,
            'terpakai' => $anggaran->terpakai,
            'sisa' => $sisa,
            'persentase' => round($persentase, 2),
            'status' => $status,
            'over_budget' => $anggaran->terpakai > $anggaran->jumlah,
        ];
    }

    /**
     * Get summary anggaran bulanan
     */
    public function getSummaryBulanan(int $bulan, int $tahun): array
    {
        $anggaran = Anggaran::with('kategori')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $totalAnggaran = $anggaran->sum('jumlah');
        $totalTerpakai = $anggaran->sum('terpakai');
        $totalSisa = $totalAnggaran - $totalTerpakai;
        $persentase = $totalAnggaran > 0 ? ($totalTerpakai / $totalAnggaran) * 100 : 0;

        // Kategori over budget
        $overBudget = $anggaran->filter(function ($item) {
            return $item->terpakai > $item->jumlah;
        });

        // Kategori mendekati limit (>80%)
        $mendekatLimit = $anggaran->filter(function ($item) {
            $persen = $item->jumlah > 0 ? ($item->terpakai / $item->jumlah) * 100 : 0;
            return $persen >= 80 && $persen < 100;
        });

        return [
            'total_anggaran' => $totalAnggaran,
            'total_terpakai' => $totalTerpakai,
            'total_sisa' => $totalSisa,
            'persentase' => round($persentase, 2),
            'status' => $this->getStatus($persentase),
            'over_budget' => $overBudget,
            'mendekati_limit' => $mendekatLimit,
            'detail' => $anggaran->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kategori' => $item->kategori->nama,
                    'jumlah' => $item->jumlah,
                    'terpakai' => $item->terpakai,
                    'sisa' => $item->jumlah - $item->terpakai,
                    'persentase' => $item->jumlah > 0 
                        ? round(($item->terpakai / $item->jumlah) * 100, 2) 
                        : 0,
                ];
            }),
        ];
    }

    /**
     * Check alert anggaran
     */
    public function checkAlert(Anggaran $anggaran): void
    {
        $persentase = $anggaran->jumlah > 0 
            ? ($anggaran->terpakai / $anggaran->jumlah) * 100 
            : 0;

        // Alert jika mencapai 80%
        if ($persentase >= 80 && $persentase < 100) {
            $this->sendNotification(
                'Anggaran Mendekati Limit',
                "Anggaran kategori {$anggaran->kategori->nama} sudah mencapai {$persentase}%",
                'warning'
            );
        }

        // Alert jika over budget
        if ($persentase >= 100) {
            $this->sendNotification(
                'Anggaran Terlampaui',
                "Anggaran kategori {$anggaran->kategori->nama} sudah terlampaui ({$persentase}%)",
                'danger'
            );
        }
    }

    /**
     * Get status berdasarkan persentase
     */
    protected function getStatus(float $persentase): string
    {
        if ($persentase >= 100) return 'danger';
        if ($persentase >= 80) return 'warning';
        if ($persentase >= 50) return 'info';
        return 'success';
    }

    /**
     * Send notification
     */
    protected function sendNotification(string $judul, string $pesan, string $tipe): void
    {
        Notifikasi::create([
            'household_id' => auth()->user()->household_id,
            'user_id' => auth()->id(),
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'dibaca' => false,
        ]);
    }

    /**
     * Get anggaran dengan filter
     */
    public function getAnggaran(array $filters = [])
    {
        $query = Anggaran::with('kategori')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if (!empty($filters['bulan'])) {
            $query->where('bulan', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        return $query->get();
    }

    /**
     * Copy anggaran dari bulan sebelumnya
     */
    public function copyFromPreviousMonth(int $bulan, int $tahun): int
    {
        $previousMonth = Carbon::create($tahun, $bulan, 1)->subMonth();
        
        $anggaranSebelumnya = Anggaran::where('bulan', $previousMonth->month)
            ->where('tahun', $previousMonth->year)
            ->get();

        $copied = 0;
        foreach ($anggaranSebelumnya as $anggaran) {
            // Cek apakah sudah ada anggaran untuk kategori ini di bulan target
            $exists = Anggaran::where('household_id', $anggaran->household_id)
                ->where('kategori_id', $anggaran->kategori_id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if (!$exists) {
                Anggaran::create([
                    'household_id' => $anggaran->household_id,
                    'kategori_id' => $anggaran->kategori_id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'jumlah' => $anggaran->jumlah,
                    'terpakai' => 0,
                ]);
                $copied++;
            }
        }

        return $copied;
    }
}
