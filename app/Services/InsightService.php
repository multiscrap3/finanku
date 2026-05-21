<?php

namespace App\Services;

use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InsightService
{
    public function __construct(
        private readonly GeminiService $geminiService
    ) {
    }

    /**
     * Generate insight bulanan berdasarkan transaksi household.
     */
    public function generateMonthlyInsight(int $householdId, int $bulan, int $tahun, bool $useAi = true): array
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $data = $this->buildMonthlyData($householdId, $start, $end);
        $insights = $this->buildRuleBasedInsights($householdId, $data, $start, $end);

        if ($useAi) {
            $aiInsight = $this->generateAiInsight($data);

            if ($aiInsight !== null) {
                $insights[] = $this->storeInsight([
                    'household_id' => $householdId,
                    'jenis' => 'pola_spending',
                    'judul' => 'Insight AI Bulanan',
                    'deskripsi' => $aiInsight,
                    'data' => $data,
                    'priority' => 1,
                    'periode_mulai' => $start->toDateString(),
                    'periode_selesai' => $end->toDateString(),
                ]);
            }
        }

        return [
            'periode' => $start->format('F Y'),
            'summary' => $data['summary'],
            'insights' => $insights,
        ];
    }

    /**
     * Ambil insight aktif untuk household.
     */
    public function getInsights(int $householdId, bool $unreadOnly = false, int $limit = 20): array
    {
        $query = DB::table('ai_insights')
            ->where('household_id', $householdId)
            ->latest('created_at');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->limit($limit)
            ->get()
            ->map(fn ($insight): array => [
                'id' => $insight->id,
                'jenis' => $insight->jenis,
                'judul' => $insight->judul,
                'deskripsi' => $insight->deskripsi,
                'data' => $insight->data ? json_decode($insight->data, true) : null,
                'priority' => $insight->priority,
                'is_read' => (bool) $insight->is_read,
                'periode_mulai' => $insight->periode_mulai,
                'periode_selesai' => $insight->periode_selesai,
                'created_at' => $insight->created_at,
            ])
            ->all();
    }

    /**
     * Tandai insight sebagai dibaca.
     */
    public function markAsRead(int $insightId, int $householdId): bool
    {
        return DB::table('ai_insights')
            ->where('id', $insightId)
            ->where('household_id', $householdId)
            ->update([
                'is_read' => true,
                'updated_at' => now(),
            ]) > 0;
    }

    private function buildMonthlyData(int $householdId, Carbon $start, Carbon $end): array
    {
        $transaksi = Transaksi::with('kategori')
            ->where('household_id', $householdId)
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $pemasukan = (float) $transaksi->where('jenis', 'pemasukan')->sum('jumlah');
        $pengeluaran = (float) $transaksi->where('jenis', 'pengeluaran')->sum('jumlah');

        $perKategori = $transaksi->where('jenis', 'pengeluaran')
            ->groupBy('kategori_id')
            ->map(function ($items): array {
                $kategori = $items->first()->kategori;

                return [
                    'kategori_id' => $items->first()->kategori_id,
                    'kategori' => $kategori?->nama ?? 'Tanpa Kategori',
                    'jumlah' => (float) $items->sum('jumlah'),
                    'total_transaksi' => $items->count(),
                ];
            })
            ->sortByDesc('jumlah')
            ->values()
            ->all();

        return [
            'bulan' => $start->month,
            'tahun' => $start->year,
            'summary' => [
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'saldo' => $pemasukan - $pengeluaran,
                'rasio_pengeluaran' => $pemasukan > 0 ? round(($pengeluaran / $pemasukan) * 100, 2) : null,
                'total_transaksi' => $transaksi->count(),
                'rata_rata_pengeluaran_harian' => round($pengeluaran / max(1, $start->daysInMonth), 2),
            ],
            'per_kategori' => $perKategori,
        ];
    }

    private function buildRuleBasedInsights(int $householdId, array $data, Carbon $start, Carbon $end): array
    {
        $insights = [];
        $summary = $data['summary'];

        if (($summary['rasio_pengeluaran'] ?? 0) >= 90) {
            $insights[] = $this->storeInsight([
                'household_id' => $householdId,
                'jenis' => 'pengeluaran_tinggi',
                'judul' => 'Pengeluaran mendekati pemasukan',
                'deskripsi' => 'Pengeluaran bulan ini sudah mencapai ' . $summary['rasio_pengeluaran'] . '% dari pemasukan. Pertimbangkan menunda pengeluaran non-prioritas.',
                'data' => $summary,
                'priority' => 2,
                'periode_mulai' => $start->toDateString(),
                'periode_selesai' => $end->toDateString(),
            ]);
        }

        $topCategory = $data['per_kategori'][0] ?? null;

        if ($topCategory !== null && $summary['pengeluaran'] > 0) {
            $percentage = round(($topCategory['jumlah'] / $summary['pengeluaran']) * 100, 2);

            if ($percentage >= 40) {
                $insights[] = $this->storeInsight([
                    'household_id' => $householdId,
                    'jenis' => 'pola_spending',
                    'judul' => 'Kategori pengeluaran dominan',
                    'deskripsi' => 'Kategori ' . $topCategory['kategori'] . ' menyumbang ' . $percentage . '% dari total pengeluaran bulan ini.',
                    'data' => [
                        'kategori' => $topCategory,
                        'persentase' => $percentage,
                    ],
                    'priority' => 1,
                    'periode_mulai' => $start->toDateString(),
                    'periode_selesai' => $end->toDateString(),
                ]);
            }
        }

        if ($summary['saldo'] < 0) {
            $insights[] = $this->storeInsight([
                'household_id' => $householdId,
                'jenis' => 'rekomendasi_hemat',
                'judul' => 'Saldo bulanan negatif',
                'deskripsi' => 'Pengeluaran lebih besar dari pemasukan. Buat batas harian dan evaluasi kategori pengeluaran terbesar.',
                'data' => $summary,
                'priority' => 2,
                'periode_mulai' => $start->toDateString(),
                'periode_selesai' => $end->toDateString(),
            ]);
        }

        return $insights;
    }

    private function generateAiInsight(array $data): ?string
    {
        try {
            return $this->geminiService->generateInsight($data);
        } catch (\Throwable $exception) {
            Log::warning('Generate AI insight gagal', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function storeInsight(array $payload): array
    {
        $now = now();

        $id = DB::table('ai_insights')->insertGetId([
            'household_id' => $payload['household_id'],
            'jenis' => $payload['jenis'],
            'judul' => $payload['judul'],
            'deskripsi' => $payload['deskripsi'],
            'data' => json_encode($payload['data'] ?? [], JSON_UNESCAPED_UNICODE),
            'priority' => $payload['priority'] ?? 0,
            'is_read' => false,
            'periode_mulai' => $payload['periode_mulai'] ?? null,
            'periode_selesai' => $payload['periode_selesai'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return [
            'id' => $id,
            'jenis' => $payload['jenis'],
            'judul' => $payload['judul'],
            'deskripsi' => $payload['deskripsi'],
            'data' => $payload['data'] ?? [],
            'priority' => $payload['priority'] ?? 0,
            'periode_mulai' => $payload['periode_mulai'] ?? null,
            'periode_selesai' => $payload['periode_selesai'] ?? null,
        ];
    }
}