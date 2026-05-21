<?php

namespace App\Services;

use App\Models\Transaksi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TokoPolaService
{
    public function getSuggestions(string $query, int $householdId, int $limit = 10): Collection
    {
        return DB::table('transaksi')
            ->select('keterangan', DB::raw('COUNT(*) as frekuensi'), DB::raw('AVG(jumlah) as rata_jumlah'))
            ->where('household_id', $householdId)
            ->where('keterangan', 'like', '%' . $query . '%')
            ->whereNotNull('keterangan')
            ->groupBy('keterangan')
            ->orderByDesc('frekuensi')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'keterangan'  => $row->keterangan,
                'frekuensi'   => (int) $row->frekuensi,
                'rata_jumlah' => round((float) $row->rata_jumlah),
            ]);
    }

    public function getFrequentKeterangan(int $householdId, string $jenis = null, int $limit = 5): Collection
    {
        return DB::table('transaksi')
            ->select('keterangan', DB::raw('COUNT(*) as frekuensi'), DB::raw('MAX(jumlah) as jumlah_terakhir'), DB::raw('MAX(kategori_id) as kategori_id'))
            ->where('household_id', $householdId)
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->groupBy('keterangan')
            ->orderByDesc('frekuensi')
            ->limit($limit)
            ->get();
    }

    public function getSuggestFromKeterangan(string $keterangan, int $householdId): array
    {
        $transaksi = DB::table('transaksi')
            ->select('jumlah', 'kategori_id', 'sumber_transaksi_id', 'jenis')
            ->where('household_id', $householdId)
            ->where('keterangan', $keterangan)
            ->latest()
            ->first();

        if (! $transaksi) {
            return [];
        }

        return [
            'jumlah'              => (float) $transaksi->jumlah,
            'kategori_id'         => $transaksi->kategori_id,
            'sumber_transaksi_id' => $transaksi->sumber_transaksi_id,
            'jenis'               => $transaksi->jenis,
        ];
    }

    public function getRecentKategoriByJenis(int $householdId, string $jenis): ?int
    {
        return DB::table('transaksi')
            ->where('household_id', $householdId)
            ->where('jenis', $jenis)
            ->whereNotNull('kategori_id')
            ->latest()
            ->value('kategori_id');
    }
}
