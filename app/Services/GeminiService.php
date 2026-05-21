<?php

namespace App\Services;

use App\Exceptions\GeminiException;
use App\Exceptions\GeminiLimitException;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;

    private string $model;

    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = (string) (env('GEMINI_API_KEY') ?: $this->getSetting('gemini_api_key', ''));
        $this->model = (string) (env('GEMINI_MODEL') ?: $this->getSetting('gemini_model', 'gemini-2.5-flash'));
    }

    public function ocrAndExtract(string $base64Image, string $mimeType): array
    {
        $cacheKey = 'gemini_ocr_' . md5($base64Image);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($base64Image, $mimeType): array {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => 'Kamu adalah asisten ekstraksi data struk belanja. Ekstrak data dari struk/screenshot ini dalam format JSON: {tanggal, nama_toko, tipe_toko, tipe_transaksi(income/outcome), items:[{nama_item,qty,harga_satuan,subtotal}], total, metode_bayar, catatan}. Jika tidak ditemukan, isi dengan null. Tanggal format: YYYY-MM-DD. Semua angka tanpa titik/koma pemisah. PENTING untuk tipe_toko: jika item yang dibeli adalah rokok atau produk tembakau (termasuk merk seperti Gudang Garam, Sampoerna, Dji Sam Soe, Marlboro, Camel, Dunhill, Djarum, A Mild, U Mild, Surya, Star Mild, Class Mild, La Mild, Wismilak, dll), isi tipe_toko dengan "rokok". Jika nama item mengandung kata rokok, kretek, sigaret, atau tembakau, tipe_toko juga harus "rokok".',
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image,
                                ],
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'response_mime_type' => 'application/json',
                ],
            ];

            $response = $this->callAPI($payload);
            $text = $this->extractText($response);

            return $this->parseJsonResponse($text);
        });
    }

    public function suggestDetail(string $nama_toko, float $total, array $history): array
    {
        $cacheKey = 'gemini_suggest_' . md5($nama_toko . $total . json_encode($history));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($nama_toko, $total, $history): array {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => sprintf(
                                    'Pengguna beli di %s total Rp %s. Berdasarkan history %d transaksi sebelumnya: %s. Suggest breakdown detail dalam JSON array items dengan field nama_item, qty, harga_satuan, subtotal.',
                                    $nama_toko,
                                    number_format($total, 0, ',', '.'),
                                    count($history),
                                    json_encode($history, JSON_UNESCAPED_UNICODE)
                                ),
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                ],
            ];

            try {
                $response = $this->callAPI($payload);
                $parsed = $this->parseJsonResponse($this->extractText($response));

                return array_is_list($parsed) ? $parsed : ($parsed['items'] ?? []);
            } catch (GeminiException $exception) {
                Log::warning('Gemini suggest detail gagal', ['message' => $exception->getMessage()]);

                return [];
            }
        });
    }

    public function generateInsight(array $data): string
    {
        $cacheKey = 'gemini_insight_' . md5(json_encode($data));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($data): string {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => sprintf(
                                    'Analisis data keuangan keluarga bulan %s %s. Data: %s. Berikan insight dalam Bahasa Indonesia yang ramah, 3-5 poin penting, dan 2-3 saran actionable. Maksimal 300 kata.',
                                    $data['bulan'] ?? '-',
                                    $data['tahun'] ?? '-',
                                    json_encode($data, JSON_UNESCAPED_UNICODE)
                                ),
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                ],
            ];

            $response = $this->callAPI($payload);

            return $this->extractText($response);
        });
    }

    public function detectAnomaly(array $transaksi, array $rata_rata): array
    {
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => sprintf(
                                'Transaksi baru: %s. Rata-rata historis: %s. Apakah ini anomali? Response JSON: {is_anomaly, alasan, severity(low/mid/high)}',
                                json_encode($transaksi, JSON_UNESCAPED_UNICODE),
                                json_encode($rata_rata, JSON_UNESCAPED_UNICODE)
                            ),
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'response_mime_type' => 'application/json',
            ],
        ];

        $response = $this->callAPI($payload);
        $parsed = $this->parseJsonResponse($this->extractText($response));

        return [
            'is_anomaly' => (bool) ($parsed['is_anomaly'] ?? false),
            'alasan' => (string) ($parsed['alasan'] ?? ''),
            'severity' => in_array(($parsed['severity'] ?? 'low'), ['low', 'mid', 'high'], true)
                ? $parsed['severity']
                : 'low',
        ];
    }

    private function callAPI(array $payload): array
    {
        if ($this->apiKey === '') {
            throw new GeminiException('GEMINI_API_KEY belum dikonfigurasi.');
        }

        $this->checkDailyLimit();

        $url = sprintf('%s/%s:generateContent', $this->baseUrl, $this->model);

        $response = Http::timeout(30)
            ->retry(2, 500)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url . '?key=' . $this->apiKey, $payload);

        $this->logApiCall($response->status(), $response->successful());

        if ($response->failed()) {
            throw new GeminiException('Gemini API error: ' . $response->body(), $response->status());
        }

        $this->incrementDailyUsage();

        $json = $response->json();

        if (! is_array($json)) {
            throw new GeminiException('Response Gemini tidak valid.');
        }

        return $json;
    }

    private function checkDailyLimit(): void
    {
        $today = Carbon::today()->toDateString();
        $resetDate = (string) $this->getSetting('gemini_reset_date', '');
        $limit = (int) $this->getSetting('ocr_daily_limit', 500);

        if ($resetDate !== $today) {
            $this->setSetting('gemini_reset_date', $today, 'string');
            $this->setSetting('gemini_ocr_used_today', 0, 'integer');

            return;
        }

        $used = (int) $this->getSetting('gemini_ocr_used_today', 0);

        if ($limit > 0 && $used >= $limit) {
            throw new GeminiLimitException('Limit harian Gemini/OCR sudah tercapai.');
        }
    }

    private function parseJsonResponse(string $text): array
    {
        $clean = trim($text);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean) ?? $clean;
        $clean = preg_replace('/\s*```$/', '', $clean) ?? $clean;
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (! is_array($decoded)) {
            throw new GeminiException('Response Gemini bukan JSON valid: ' . mb_substr($clean, 0, 500));
        }

        return $decoded;
    }

    private function extractText(array $response): string
    {
        $text = data_get($response, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new GeminiException('Gemini tidak mengembalikan teks yang valid.');
        }

        return trim($text);
    }

    private function incrementDailyUsage(): void
    {
        $used = (int) $this->getSetting('gemini_ocr_used_today', 0);
        $this->setSetting('gemini_ocr_used_today', $used + 1, 'integer');
    }

    private function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    private function setSetting(string $key, mixed $value, string $type = 'string'): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key, 'household_id' => null],
            ['value' => $value, 'type' => $type]
        );
    }

    private function logApiCall(int $statusCode, bool $success): void
    {
        try {
            if (class_exists(AuditLog::class)) {
                AuditLog::query()->create([
                    'household_id' => session('active_household_id'),
                    'user_id' => Auth::id(),
                    'action' => 'gemini_api_call',
                    'model' => self::class,
                    'model_id' => null,
                    'old_data' => null,
                    'new_data' => [
                        'model' => $this->model,
                        'status_code' => $statusCode,
                        'success' => $success,
                    ],
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->userAgent(),
                ]);
            }
        } catch (\Throwable $throwable) {
            Log::warning('Gagal mencatat Gemini API call', ['message' => $throwable->getMessage()]);
        }
    }
}