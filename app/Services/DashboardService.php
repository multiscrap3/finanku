<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\SumberTransaksi;
use App\Models\Anggaran;
use App\Models\Tabungan;
use App\Models\HutangPiutang;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get dashboard summary
     */
    public function getSummary(): array
    {
        $householdId = auth()->user()->household_id;
        $bulanIni = Carbon::now();

        return [
            'saldo_total' => $this->getTotalSaldo(),
            'transaksi_bulan_ini' => $this->getTransaksiBulanIni($bulanIni),
            'anggaran_summary' => $this->getAnggaranSummary($bulanIni),
            'tabungan_summary' => $this->getTabunganSummary(),
            'hutang_piutang_summary' => $this->getHutangPiutangSummary(),
            'transaksi_terbaru' => $this->getTransaksiTerbaru(),
            'chart_data' => $this->getChartData(),
        ];
    }

    /**
     * Get total saldo dari semua sumber transaksi
     */
    protected function getTotalSaldo(): float
    {
        return SumberTransaksi::sum('saldo_saat_ini');
    }

    /**
     * Get transaksi bulan ini
     */
    protected function getTransaksiBulanIni(Carbon $bulan): array
    {
        $pemasukan = Transaksi::where('jenis', 'pemasukan')
            ->whereYear('tanggal', $bulan->year)
            ->whereMonth('tanggal', $bulan->month)
            ->sum('jumlah');

        $pengeluaran = Transaksi::where('jenis', 'pengeluaran')
            ->whereYear('tanggal', $bulan->year)
            ->whereMonth('tanggal', $bulan->month)
            ->sum('jumlah');

        $selisih = $pemasukan - $pengeluaran;
        $persentase = $pemasukan > 0 ? ($pengeluaran / $pemasukan) * 100 : 0;

        return [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'selisih' => $selisih,
            'persentase_pengeluaran' => round($persentase, 2),
        ];
    }

    /**
     * Get anggaran summary
     */
    protected function getAnggaranSummary(Carbon $bulan): array
    {
        $anggaran = Anggaran::where('bulan', $bulan->month)
            ->where('tahun', $bulan->year)
            ->get();

        $totalAnggaran = $anggaran->sum('jumlah');
        $totalTerpakai = $anggaran->sum('terpakai');
        $sisa = $totalAnggaran - $totalTerpakai;
        $persentase = $totalAnggaran > 0 ? ($totalTerpakai / $totalAnggaran) * 100 : 0;

        // Cek kategori yang over budget
        $overBudget = $anggaran->filter(function ($item) {
            return $item->terpakai > $item->jumlah;
        })->count();

        return [
            'total_anggaran' => $totalAnggaran,
            'total_terpakai' => $totalTerpakai,
            'sisa' => $sisa,
            'persentase' => round($persentase, 2),
            'over_budget_count' => $overBudget,
            'status' => $this->getAnggaranStatus($persentase),
        ];
    }

    /**
     * Get status anggaran berdasarkan persentase
     */
    protected function getAnggaranStatus(float $persentase): string
    {
        if ($persentase >= 100) return 'danger';
        if ($persentase >= 80) return 'warning';
        if ($persentase >= 50) return 'info';
        return 'success';
    }

    /**
     * Get tabungan summary
     */
    protected function getTabunganSummary(): array
    {
        $tabungan = Tabungan::where('status', 'aktif')->get();

        $totalTarget = $tabungan->sum('target_jumlah');
        $totalTerkumpul = $tabungan->sum('terkumpul');
        $persentase = $totalTarget > 0 ? ($totalTerkumpul / $totalTarget) * 100 : 0;

        return [
            'total_tabungan' => $tabungan->count(),
            'total_target' => $totalTarget,
            'total_terkumpul' => $totalTerkumpul,
            'persentase' => round($persentase, 2),
            'tercapai' => $tabungan->where('status', 'tercapai')->count(),
        ];
    }

    /**
     * Get hutang piutang summary
     */
    protected function getHutangPiutangSummary(): array
    {
        $hutang = HutangPiutang::where('jenis', 'hutang')
            ->where('status', '!=', 'lunas')
            ->get();

        $piutang = HutangPiutang::where('jenis', 'piutang')
            ->where('status', '!=', 'lunas')
            ->get();

        return [
            'total_hutang' => $hutang->sum('jumlah_total'),
            'hutang_terbayar' => $hutang->sum('jumlah_terbayar'),
            'hutang_sisa' => $hutang->sum(function ($item) {
                return $item->jumlah_total - $item->jumlah_terbayar;
            }),
            'total_piutang' => $piutang->sum('jumlah_total'),
            'piutang_terbayar' => $piutang->sum('jumlah_terbayar'),
            'piutang_sisa' => $piutang->sum(function ($item) {
                return $item->jumlah_total - $item->jumlah_terbayar;
            }),
            'jatuh_tempo_minggu_ini' => $this->getJatuhTempoMingguIni(),
        ];
    }

    /**
     * Get hutang/piutang yang jatuh tempo minggu ini
     */
    protected function getJatuhTempoMingguIni(): int
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return HutangPiutang::where('status', '!=', 'lunas')
            ->whereBetween('tanggal_jatuh_tempo', [$startOfWeek, $endOfWeek])
            ->count();
    }

    /**
     * Get transaksi terbaru (5 terakhir)
     */
    protected function getTransaksiTerbaru()
    {
        return Transaksi::with(['kategori', 'sumberTransaksi', 'user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get data untuk chart (6 bulan terakhir)
     */
    protected function getChartData(): array
    {
        $months = [];
        $pemasukan = [];
        $pengeluaran = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $pemasukanBulan = Transaksi::where('jenis', 'pemasukan')
                ->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $pengeluaranBulan = Transaksi::where('jenis', 'pengeluaran')
                ->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $pemasukan[] = $pemasukanBulan;
            $pengeluaran[] = $pengeluaranBulan;
        }

        return [
            'labels' => $months,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
        ];
    }

    /**
     * Get pengeluaran per kategori (bulan ini)
     */
    public function getPengeluaranPerKategori(): array
    {
        $bulanIni = Carbon::now();

        $data = Transaksi::select('kategori_id', DB::raw('SUM(jumlah) as total'))
            ->with('kategori')
            ->where('jenis', 'pengeluaran')
            ->whereYear('tanggal', $bulanIni->year)
            ->whereMonth('tanggal', $bulanIni->month)
            ->groupBy('kategori_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('kategori.nama')->toArray(),
            'values' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * Get saldo per sumber transaksi
     */
    public function getSaldoPerSumber(): array
    {
        $sumber = SumberTransaksi::orderBy('saldo_saat_ini', 'desc')->get();

        return [
            'labels' => $sumber->pluck('nama')->toArray(),
            'values' => $sumber->pluck('saldo_saat_ini')->toArray(),
        ];
    }
}
