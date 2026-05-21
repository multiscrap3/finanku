# AI & OCR Integration Progress

## Ringkasan

Integrasi fitur AI/OCR backend sudah ditambahkan ke project Finanku.

## File yang Ditambahkan

### `app/Http/Controllers/OCRController.php`

Endpoint:
- `extract(Request $request)`
  - Upload gambar struk/screenshot.
  - Validasi file `jpg`, `jpeg`, `png`, `webp`, maksimal 5 MB.
  - Proses upload melalui `OCRService`.
  - Ekstraksi OCR + struktur transaksi melalui `GeminiService`.
  - Simpan riwayat ke tabel `ocr_history`.
  - Return hasil OCR dan `suggested_transaksi`.

- `history(Request $request)`
  - Mengambil riwayat OCR berdasarkan `household_id` user login.
  - Mendukung pagination `per_page`.

### `app/Http/Controllers/AIController.php`

Endpoint:
- `checkDuplicate(Request $request)`
  - Mengecek kandidat transaksi duplikat menggunakan `DedupService`.
  - Mengembalikan status `is_duplicate`, jumlah kandidat, dan score.

- `detectAnomaly(Request $request)`
  - Mengecek apakah calon transaksi anomali menggunakan `AnomalyDetectionService`.
  - Mendukung opsi `use_ai`.

- `scanAnomalies(Request $request)`
  - Scan transaksi anomali dalam rentang tanggal.

- `generateInsights(Request $request)`
  - Generate insight AI untuk household user melalui `InsightService`.

## File yang Diubah

### `app/Http/Controllers/TransaksiController.php`

Perubahan:
- Menambahkan dependency:
  - `DedupService`
  - `AnomalyDetectionService`
- Pada `store()`:
  - Mengecek transaksi duplikat sebelum create.
  - Mengecek anomali sebelum create.
  - Menyimpan warning ke session:
    - `warning_duplicate`
    - `warning_anomaly`
- Pada `update()`:
  - Mengecek duplikat dengan `exclude_id` transaksi saat ini.
  - Mengecek anomali.
  - Menyimpan warning ke session jika terdeteksi.

### `routes/web.php`

Menambahkan import:
- `AIController`
- `OCRController`

Menambahkan route API-like di prefix `/api` dengan middleware `auth`:

#### OCR
- `POST /api/ocr/extract`
  - Name: `api.ocr.extract`
- `GET /api/ocr/history`
  - Name: `api.ocr.history`

#### AI
- `POST /api/ai/duplicate-check`
  - Name: `api.ai.duplicate-check`
- `POST /api/ai/anomaly-detect`
  - Name: `api.ai.anomaly-detect`
- `GET /api/ai/anomalies/scan`
  - Name: `api.ai.anomalies.scan`
- `POST /api/ai/insights/generate`
  - Name: `api.ai.insights.generate`

## Verifikasi

Syntax PHP berhasil dicek untuk file berikut:

```text
No syntax errors detected in ..\Finanku\app\Http\Controllers\AIController.php
No syntax errors detected in ..\Finanku\app\Http\Controllers\OCRController.php
No syntax errors detected in ..\Finanku\app\Http\Controllers\TransaksiController.php
No syntax errors detected in ..\Finanku\routes\web.php
```

Command yang berhasil:

```bat
dir ..\Finanku\app\Http\Controllers\AIController.php && php -l ..\Finanku\app\Http\Controllers\AIController.php && php -l ..\Finanku\app\Http\Controllers\OCRController.php && php -l ..\Finanku\app\Http\Controllers\TransaksiController.php && php -l ..\Finanku\routes\web.php
```

## Catatan Validasi Route

`php artisan route:list --path=api` belum bisa dijalankan karena dependency Composer belum tersedia:

```text
Failed opening required 'C:\laragon\www\Finanku/vendor/autoload.php'
```

Untuk menjalankan validasi route penuh, install dependency Composer terlebih dahulu:

```bat
cd C:\laragon\www\Finanku
composer install
php artisan route:list --path=api
```

## Status

- Controller OCR: selesai.
- Controller AI: selesai.
- Integrasi dedup/anomali ke transaksi: selesai.
- Route API AI/OCR: selesai.
- Syntax check: selesai.
- Route-list runtime: tertunda sampai `vendor/` tersedia.