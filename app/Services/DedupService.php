<?php

namespace App\Services;

use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DedupService
{
    /**
     * Cari kandidat transaksi duplikat berdasarkan data transaksi baru.
     */
    public function findDuplicates(array $data, int $dayWindow = 3, float $amountTolerance = 0.0): Collection
    {
        $householdId = (int) ($data['household_id'] ?? 0);
        $jumlah = (float) ($data['jumlah'] ?? 0);
        $tanggal = Carbon::parse($data['tanggal'] ?? now());
        $jenis = $data['jenis'] ?? null;

        $query = Transaksi::with(['kategori', 'sumberTransaksi'])
            ->where('household_id', $householdId)
            ->whereBetween('tanggal', [
                $tanggal->copy()->subDays($dayWindow)->toDateString(),
                $tanggal->copy()->addDays($dayWindow)->toDateString(),
            ])
            ->whereBetween('jumlah', [
                max(0, $jumlah - $amountTolerance),
                $jumlah + $amountTolerance,
            ]);

        if ($jenis !== null) {
            $query->where('jenis', $jenis);
        }

        if (! empty($data['exclude_id'])) {
            $query->where('id', '!=', (int) $data['exclude_id']);
        }

        if (! empty($data['kategori_id'])) {
            $query->where(function ($query) use ($data) {
                $query->where('kategori_id', $data['kategori_id'])
                    ->orWhereNull('kategori_id');
            });
        }

        if (! empty($data['sumber_transaksi_id'])) {
            $query->where(function ($query) use ($data) {
                $query->where('sumber_transaksi_id', $data['sumber_transaksi_id'])
                    ->orWhereNull('sumber_transaksi_id');
            });
        }

        return $query->latest('tanggal')
            ->limit(20)
            ->get()
            ->filter(fn (Transaksi $transaksi): bool => $this->score($data, $transaksi) >= 70)
            ->values();
    }

    /**
     * Cek apakah data transaksi kemungkinan duplikat.
     */
    public function isDuplicate(array $data, int $dayWindow = 3, float $amountTolerance = 0.0): bool
    {
        return $this->findDuplicates($data, $dayWindow, $amountTolerance)->isNotEmpty();
    }

    /**
     * Berikan skor kemiripan 0-100 terhadap transaksi existing.
     */
    public function score(array $data, Transaksi $existing): int
    {
        $score = 0;

        if ((float) ($data['jumlah'] ?? 0) === (float) $existing->jumlah) {
            $score += 35;
        }

        if (($data['jenis'] ?? null) === $existing->jenis) {
            $score += 20;
        }

        if (! empty($data['tanggal']) && Carbon::parse($data['tanggal'])->isSameDay($existing->tanggal)) {
            $score += 15;
        }

        if (! empty($data['kategori_id']) && (int) $data['kategori_id'] === (int) $existing->kategori_id) {
            $score += 10;
        }

        if (! empty($data['sumber_transaksi_id']) && (int) $data['sumber_transaksi_id'] === (int) $existing->sumber_transaksi_id) {
            $score += 10;
        }

        $newNote = $this->normalizeText((string) ($data['keterangan'] ?? ''));
        $oldNote = $this->normalizeText((string) ($existing->keterangan ?? ''));

        if ($newNote !== '' && $oldNote !== '') {
            similar_text($newNote, $oldNote, $percentage);

            if ($percentage >= 80) {
                $score += 10;
            } elseif ($percentage >= 50) {
                $score += 5;
            }
        }

        return min(100, $score);
    }

    /**
     * Format response aman untuk API/controller.
     */
    public function formatDuplicateResponse(array $data): array
    {
        $duplicates = $this->findDuplicates($data);

        return [
            'is_duplicate' => $duplicates->isNotEmpty(),
            'total_candidates' => $duplicates->count(),
            'candidates' => $duplicates->map(fn (Transaksi $transaksi): array => [
                'id' => $transaksi->id,
                'tanggal' => optional($transaksi->tanggal)->toDateString(),
                'jenis' => $transaksi->jenis,
                'jumlah' => (float) $transaksi->jumlah,
                'kategori' => $transaksi->kategori?->nama,
                'sumber_transaksi' => $transaksi->sumberTransaksi?->nama,
                'keterangan' => $transaksi->keterangan,
                'score' => $this->score($data, $transaksi),
            ])->values()->all(),
        ];
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return $text;
    }
}