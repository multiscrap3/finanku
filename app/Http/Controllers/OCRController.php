<?php

namespace App\Http\Controllers;

use App\Exceptions\GeminiException;
use App\Exceptions\GeminiLimitException;
use App\Services\GeminiService;
use App\Services\OCRService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OCRController extends Controller
{
    public function __construct(
        private readonly OCRService $ocrService,
        private readonly GeminiService $geminiService
    ) {
    }

    /**
     * Upload struk/screenshot, jalankan OCR + ekstraksi Gemini, lalu simpan history.
     */
    public function extract(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $upload = $this->ocrService->processUpload($validated['image']);

        if (! $upload['valid']) {
            return response()->json([
                'success' => false,
                'message' => $upload['error'],
                'data' => $upload,
            ], 422);
        }

        $historyId = DB::table('ocr_history')->insertGetId([
            'household_id' => $user->household_id,
            'user_id' => $user->id,
            'transaksi_id' => null,
            'image_path' => $upload['path'],
            'ocr_result' => null,
            'detected_amount' => null,
            'detected_date' => null,
            'detected_merchant' => null,
            'status' => 'processing',
            'error_message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $result = $this->geminiService->ocrAndExtract($upload['base64'], $upload['mime_type']);

            DB::table('ocr_history')
                ->where('id', $historyId)
                ->update([
                    'ocr_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                    'detected_amount' => $result['total'] ?? null,
                    'detected_date' => $result['tanggal'] ?? null,
                    'detected_merchant' => $result['nama_toko'] ?? null,
                    'status' => 'success',
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'OCR berhasil diproses.',
                'data' => [
                    'history_id' => $historyId,
                    'image_path' => $upload['path'],
                    'original_name' => $upload['original_name'],
                    'size_kb' => $upload['size_kb'],
                    'result' => $result,
                    'suggested_transaksi' => $this->buildSuggestedTransaksi($result),
                ],
            ]);
        } catch (GeminiLimitException|GeminiException $exception) {
            $this->markFailed($historyId, $exception->getMessage());

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => [
                    'history_id' => $historyId,
                    'image_path' => $upload['path'],
                ],
            ], 422);
        } catch (Throwable $exception) {
            $this->markFailed($historyId, $exception->getMessage());

            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'OCR gagal diproses.',
                'data' => [
                    'history_id' => $historyId,
                    'image_path' => $upload['path'],
                ],
            ], 500);
        }
    }

    /**
     * Ambil riwayat OCR milik household user.
     */
    public function history(Request $request): JsonResponse
    {
        $perPage = min((int) $request->integer('per_page', 20), 100);

        $history = DB::table('ocr_history')
            ->where('household_id', $request->user()->household_id)
            ->latest()
            ->paginate($perPage)
            ->through(fn ($item): array => [
                'id' => $item->id,
                'transaksi_id' => $item->transaksi_id,
                'image_path' => $item->image_path,
                'ocr_result' => $item->ocr_result ? json_decode($item->ocr_result, true) : null,
                'detected_amount' => $item->detected_amount !== null ? (float) $item->detected_amount : null,
                'detected_date' => $item->detected_date,
                'detected_merchant' => $item->detected_merchant,
                'status' => $item->status,
                'error_message' => $item->error_message,
                'created_at' => $item->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    private function markFailed(int $historyId, string $message): void
    {
        DB::table('ocr_history')
            ->where('id', $historyId)
            ->update([
                'status' => 'failed',
                'error_message' => $message,
                'updated_at' => now(),
            ]);
    }

    private function buildSuggestedTransaksi(array $result): array
    {
        $tipeTransaksi = $result['tipe_transaksi'] ?? 'outcome';

        return [
            'jenis' => $tipeTransaksi === 'income' ? 'pemasukan' : 'pengeluaran',
            'jumlah' => $result['total'] ?? null,
            'tanggal' => $result['tanggal'] ?? now()->toDateString(),
            'keterangan' => trim(($result['nama_toko'] ?? '') . ' ' . ($result['catatan'] ?? '')) ?: null,
            'items' => $result['items'] ?? [],
            'metode_bayar' => $result['metode_bayar'] ?? null,
        ];
    }
}