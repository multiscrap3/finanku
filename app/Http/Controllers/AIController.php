<?php

namespace App\Http\Controllers;

use App\Services\AnomalyDetectionService;
use App\Services\DedupService;
use App\Services\InsightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(
        private readonly DedupService $dedupService,
        private readonly AnomalyDetectionService $anomalyDetectionService,
        private readonly InsightService $insightService
    ) {
    }

    /**
     * Cek kandidat transaksi duplikat sebelum data disimpan.
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:pemasukan,pengeluaran,transfer'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'tanggal' => ['required', 'date'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
            'sumber_transaksi_id' => ['nullable', 'integer', 'exists:sumber_transaksi,id'],
            'keterangan' => ['nullable', 'string'],
            'exclude_id' => ['nullable', 'integer', 'exists:transaksi,id'],
            'day_window' => ['nullable', 'integer', 'min:0', 'max:30'],
            'amount_tolerance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['household_id'] = $request->user()->household_id;

        $duplicates = $this->dedupService->findDuplicates(
            $validated,
            (int) ($validated['day_window'] ?? 3),
            (float) ($validated['amount_tolerance'] ?? 0)
        );

        return response()->json([
            'success' => true,
            'data' => [
                'is_duplicate' => $duplicates->isNotEmpty(),
                'total_candidates' => $duplicates->count(),
                'candidates' => $duplicates->map(fn ($transaksi): array => [
                    'id' => $transaksi->id,
                    'tanggal' => optional($transaksi->tanggal)->toDateString(),
                    'jenis' => $transaksi->jenis,
                    'jumlah' => (float) $transaksi->jumlah,
                    'kategori' => $transaksi->kategori?->nama,
                    'sumber_transaksi' => $transaksi->sumberTransaksi?->nama,
                    'keterangan' => $transaksi->keterangan,
                    'score' => $this->dedupService->score($validated, $transaksi),
                ])->values()->all(),
            ],
        ]);
    }

    /**
     * Deteksi apakah calon transaksi merupakan anomali.
     */
    public function detectAnomaly(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:pemasukan,pengeluaran,transfer'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'tanggal' => ['required', 'date'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
            'sumber_transaksi_id' => ['nullable', 'integer', 'exists:sumber_transaksi,id'],
            'keterangan' => ['nullable', 'string'],
            'use_ai' => ['nullable', 'boolean'],
        ]);

        $validated['household_id'] = $request->user()->household_id;

        return response()->json([
            'success' => true,
            'data' => $this->anomalyDetectionService->detect(
                $validated,
                $request->boolean('use_ai', true)
            ),
        ]);
    }

    /**
     * Scan anomali transaksi pada periode tertentu.
     */
    public function scanAnomalies(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'use_ai' => ['nullable', 'boolean'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->anomalyDetectionService->scanPeriod(
                (int) $request->user()->household_id,
                $validated['start_date'],
                $validated['end_date'],
                $request->boolean('use_ai', false)
            ),
        ]);
    }

    /**
     * Generate insight AI untuk household user.
     */
    public function generateInsights(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insight berhasil dibuat.',
            'data' => $this->insightService->generateForHousehold(
                (int) $request->user()->household_id,
                $validated['periode_mulai'] ?? now()->startMonth()->toDateString(),
                $validated['periode_selesai'] ?? now()->endMonth()->toDateString()
            ),
        ]);
    }
}