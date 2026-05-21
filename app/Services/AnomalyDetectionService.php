<?php

namespace App\Services;

use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnomalyDetectionService
{
    public function __construct(
        private readonly GeminiService $geminiService
    ) {
    }

    /**
     * Deteksi anomali untuk data transaksi baru.
     */
    public function detect(array $transaksiData, bool $useAi = true): array
    {
        $householdId = (int) ($transaksiData['household_id'] ?? 0);
        $kategoriId = $transaksiData['kategori_id'] ?? null;
        $jenis = (string) ($transaksiData['jenis'] ?? 'pengeluaran');

        $stats = $this->buildHistoricalStats($householdId, $jenis, $kategoriId);
        $ruleResult = $this->detectByRules($transaksiData, $stats);

        $aiResult = null;
        if ($useAi && $stats['total_sample'] >= 5) {
            $aiResult = $this->detectByAi($transaksiData, $stats);
        }

        $result = $this->mergeResults($ruleResult, $aiResult);

        if ($result['is_anomaly']) {
            $this->storeAnomalyInsight($transaksiData, $result, $stats);
        }

        return [
            'is_anomaly' => $result['is_anomaly'],
            'severity' => $result['severity'],
            'alasan' => $result['alasan'],
            'score' => $result['score'],
            'stats' => $stats,
            'source' => $aiResult !== null ? 'rules+ai' : 'rules',
        ];
    }

    /**
     * Deteksi anomali untuk transaksi yang sudah tersimpan.
     */
    public function detectForTransaksi(Transaksi $transaksi, bool $useAi = true): array
    {
        return $this->detect([
            'id' => $transaksi->id,
            'household_id' => $transaksi->household_id,
            'kategori_id' => $transaksi->kategori_id,
            'sumber_transaksi_id' => $transaksi->sumber_transaksi_id,
            'jenis' => $transaksi->jenis,
            'jumlah' => (float) $transaksi->jumlah,
            'tanggal' => optional($transaksi->tanggal)->toDateString(),
            'keterangan' => $transaksi->keterangan,
        ], $useAi);
    }

    /**
     * Scan transaksi periode tertentu dan kembalikan daftar anomali.
     */
    public function scanPeriod(int $householdId, string $startDate, string $endDate, bool $useAi = false): array
    {
        return Transaksi::with(['kategori', 'sumberTransaksi'])
            ->where('household_id', $householdId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get()
            ->map(fn (Transaksi $transaksi): array => [
                'transaksi' => [
                    'id' => $transaksi->id,
                    'tanggal' => optional($transaksi->tanggal)->toDateString(),
                    'jenis' => $transaksi->jenis,
                    'jumlah' => (float) $transaksi->jumlah,
                    'kategori' => $transaksi->kategori?->nama,
                    'sumber_transaksi' => $transaksi->sumberTransaksi?->nama,
                    'keterangan' => $transaksi->keterangan,
                ],
                'detection' => $this->detectForTransaksi($transaksi, $useAi),
            ])
            ->filter(fn (array $item): bool => $item['detection']['is_anomaly'])
            ->values()
            ->all();
    }

    private function buildHistoricalStats(int $householdId, string $jenis, mixed $kategoriId = null): array
    {
        $query = Transaksi::query()
            ->where('household_id', $householdId)
            ->where('jenis', $jenis)
            ->where('tanggal', '>=', Carbon::now()->subMonths(6)->toDateString());

        if ($kategoriId !== null) {
            $query->where('kategori_id', $kategoriId);
        }

        $values = $query->pluck('jumlah')
            ->map(fn ($jumlah): float => (float) $jumlah)
            ->values();

        $count = $values->count();
        $average = $count > 0 ? round($values->avg(), 2) : 0.0;
        $max = $count > 0 ? (float) $values->max() : 0.0;
        $min = $count > 0 ? (float) $values->min() : 0.0;

        $variance = 0.0;
        if ($count > 1) {
            $variance = $values
                ->map(fn (float $jumlah): float => ($jumlah - $average) ** 2)
                ->sum() / ($count - 1);
        }

        $stdDev = round(sqrt($variance), 2);

        return [
            'total_sample' => $count,
            'rata_rata' => $average,
            'minimum' => $min,
            'maximum' => $max,
            'std_dev' => $stdDev,
            'threshold_high' => round($average + (2 * $stdDev), 2),
            'threshold_extreme' => round($average + (3 * $stdDev), 2),
            'periode_bulan' => 6,
        ];
    }

    private function detectByRules(array $transaksiData, array $stats): array
    {
        $jumlah = (float) ($transaksiData['jumlah'] ?? 0);
        $score = 0;
        $reasons = [];

        if ($stats['total_sample'] < 5) {
            return [
                'is_anomaly' => false,
                'severity' => 'low',
                'alasan' => 'Data historis belum cukup untuk mendeteksi anomali.',
                'score' => 0,
            ];
        }

        if ($stats['threshold_extreme'] > 0 && $jumlah >= $stats['threshold_extreme']) {
            $score += 80;
            $reasons[] = 'Nominal jauh di atas pola historis.';
        } elseif ($stats['threshold_high'] > 0 && $jumlah >= $stats['threshold_high']) {
            $score += 55;
            $reasons[] = 'Nominal lebih tinggi dari rata-rata historis.';
        }

        if ($stats['rata_rata'] > 0 && $jumlah >= ($stats['rata_rata'] * 2.5)) {
            $score += 25;
            $reasons[] = 'Nominal minimal 2,5x dari rata-rata.';
        }

        if ($stats['maximum'] > 0 && $jumlah > $stats['maximum']) {
            $score += 20;
            $reasons[] = 'Nominal melebihi transaksi terbesar sebelumnya.';
        }

        $severity = match (true) {
            $score >= 80 => 'high',
            $score >= 55 => 'mid',
            default => 'low',
        };

        return [
            'is_anomaly' => $score >= 55,
            'severity' => $severity,
            'alasan' => $reasons !== [] ? implode(' ', $reasons) : 'Transaksi masih dalam pola normal.',
            'score' => min(100, $score),
        ];
    }

    private function detectByAi(array $transaksiData, array $stats): ?array
    {
        try {
            $result = $this->geminiService->detectAnomaly($transaksiData, $stats);

            return [
                'is_anomaly' => (bool) ($result['is_anomaly'] ?? false),
                'severity' => $this->normalizeSeverity((string) ($result['severity'] ?? 'low')),
                'alasan' => (string) ($result['alasan'] ?? ''),
                'score' => ($result['is_anomaly'] ?? false) ? 70 : 0,
            ];
        } catch (\Throwable $exception) {
            Log::warning('AI anomaly detection gagal', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function mergeResults(array $ruleResult, ?array $aiResult): array
    {
        if ($aiResult === null) {
            return $ruleResult;
        }

        $isAnomaly = $ruleResult['is_anomaly'] || $aiResult['is_anomaly'];
        $score = max((int) $ruleResult['score'], (int) $aiResult['score']);
        $severity = $this->highestSeverity($ruleResult['severity'], $aiResult['severity']);

        $reasons = array_filter([
            $ruleResult['is_anomaly'] ? 'Rule: ' . $ruleResult['alasan'] : null,
            $aiResult['is_anomaly'] ? 'AI: ' . $aiResult['alasan'] : null,
        ]);

        return [
            'is_anomaly' => $isAnomaly,
            'severity' => $severity,
            'alasan' => $reasons !== [] ? implode(' ', $reasons) : 'Transaksi masih dalam pola normal.',
            'score' => $score,
        ];
    }

    private function storeAnomalyInsight(array $transaksiData, array $result, array $stats): void
    {
        if (empty($transaksiData['household_id'])) {
            return;
        }

        DB::table('ai_insights')->insert([
            'household_id' => $transaksiData['household_id'],
            'jenis' => 'anomali',
            'judul' => 'Transaksi tidak biasa terdeteksi',
            'deskripsi' => $result['alasan'],
            'data' => json_encode([
                'transaksi' => $transaksiData,
                'detection' => $result,
                'stats' => $stats,
            ], JSON_UNESCAPED_UNICODE),
            'priority' => $result['severity'] === 'high' ? 2 : 1,
            'is_read' => false,
            'periode_mulai' => $transaksiData['tanggal'] ?? null,
            'periode_selesai' => $transaksiData['tanggal'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function normalizeSeverity(string $severity): string
    {
        return match ($severity) {
            'high' => 'high',
            'mid', 'medium' => 'mid',
            default => 'low',
        };
    }

    private function highestSeverity(string $first, string $second): string
    {
        $rank = [
            'low' => 1,
            'mid' => 2,
            'high' => 3,
        ];

        return ($rank[$first] ?? 1) >= ($rank[$second] ?? 1) ? $first : $second;
    }
}