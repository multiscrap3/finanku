# PROMPT.md — Master Prompt FinanKu
# Aplikasi Keuangan Keluarga SaaS-Ready
# Untuk: Claude Opus via Claude Code (VS Code)
# Versi: 1.0.0

---

## INSTRUKSI WAJIB UNTUK CLAUDE CODE

Setiap kali kamu selesai membuat 1 file atau 1 grup file, kamu WAJIB:
1. Update PROGRESS.md → tandai item yang selesai dengan ✅
2. Tulis timestamp selesai di PROGRESS.md
3. Tulis nama file yang baru dibuat di section "Terakhir Dibuat"
4. Jika ada error saat menjalankan artisan/composer → catat di section "Error Log" di PROGRESS.md
5. Informasikan ke user: file apa yang selesai, apa yang selanjutnya

Jangan pernah lanjut ke tahap berikutnya tanpa update PROGRESS.md terlebih dahulu.

---

## KONTEKS PROYEK

**Nama Aplikasi:** FinanKu
**Tagline:** Asisten Keuangan Keluarga Berbasis AI
**Tujuan:** Aplikasi web manajemen keuangan keluarga yang:
1. Saat ini untuk internal testing (1-5 household)
2. Dirancang SaaS-ready dari hari pertama (multi-tenant)
3. Di-deploy ke shared hosting RumahWeb paket Small (tanpa SSH, deploy manual via cPanel)
4. Semua fitur SaaS layer (payment, billing, landing page) HANYA sebagai struktur/planning — belum diimplementasi aktif di UI

---

## TECH STACK

| Komponen | Teknologi | Versi |
|---|---|---|
| Backend | Laravel | 11.x |
| PHP | PHP | 8.2+ |
| Database | MySQL | 8.0+ |
| Frontend | Blade + TailwindCSS | CDN (tanpa build) |
| JS Interaktif | Alpine.js | CDN |
| Charts | Chart.js | CDN |
| AI/OCR | Gemini 2.5 Flash API | Free tier |
| Excel | maatwebsite/laravel-excel | 3.x |
| PDF | barryvdh/laravel-dompdf | latest |
| Image | intervention/image | 3.x |
| Auth | Laravel Breeze | latest |
| API Auth | Laravel Sanctum | latest |

---

## KETERBATASAN HOSTING

- Shared hosting RumahWeb Small — TANPA SSH
- TANPA Laravel Queue/Supervisor → semua proses SYNCHRONOUS
- TANPA Cron job server → gunakan cron-job.org (eksternal, gratis)
- Composer dijalankan LOKAL, folder vendor di-upload bersama project
- TailwindCSS via CDN — BUKAN build process (tidak ada Node.js di server)
- TANPA Redis → gunakan database cache driver
- Max upload file: 5MB per file
- TANPA websocket/pusher → notifikasi via polling atau page refresh

---

## ARSITEKTUR SISTEM

### Multi-Tenancy
- Setiap household = 1 tenant yang TERISOLASI PENUH
- Semua Model keuangan pakai Global Scope `household_id` via Trait `BelongsToHousehold`
- User TIDAK BISA akses data household lain dalam kondisi apapun
- Query tanpa household scope HANYA diizinkan di SuperadminController

### Layer Aplikasi
```
PUBLIK     : /login, /register, /landing (kosong dulu)
AUTH       : /onboarding → /dashboard
APLIKASI   : Semua fitur per household (middleware: auth + verified + household)
SUPERADMIN : /superadmin/* (middleware: auth + superadmin-role)
API        : /api/v1/* (middleware: auth:sanctum)
CRON       : /cron/* (middleware: cron-secret)
```

### Pola Arsitektur
```
Request → Middleware → Controller → Service → Model → Database
                         ↓
                    Form Request (validasi)
                         ↓
                    Return View / JSON
```

---

## DATABASE LENGKAP (23 Tabel — SaaS-Ready)

### URUTAN MIGRATION (ikuti urutan ini karena ada foreign key)

#### GRUP 1: System & SaaS Tables

**001_create_plans_table**
```sql
id, nama VARCHAR(100), slug VARCHAR(100) UNIQUE,
harga DECIMAL(15,2) DEFAULT 0,
max_anggota INT DEFAULT -1,        -- -1 = unlimited
max_transaksi INT DEFAULT -1,
max_ocr INT DEFAULT -1,
fitur JSON,                         -- list fitur aktif
is_active BOOLEAN DEFAULT TRUE,
timestamps
```
Seed: 1 record → {nama:'Internal Testing', slug:'internal', harga:0, semua max:-1, fitur:["all"]}

**002_create_users_table**
```sql
id, nama VARCHAR(255), email VARCHAR(255) UNIQUE,
password VARCHAR(255), avatar VARCHAR(255) NULL,
email_verified_at TIMESTAMP NULL,
referral_code VARCHAR(20) UNIQUE,   -- generate otomatis saat register
referred_by BIGINT NULL FK users.id,
last_login_at TIMESTAMP NULL,
is_active BOOLEAN DEFAULT TRUE,
remember_token VARCHAR(100) NULL,
timestamps, deleted_at
```

**003_create_households_table**
```sql
id, nama VARCHAR(255), slug VARCHAR(255) UNIQUE,
kode_invite VARCHAR(20) UNIQUE,     -- generate otomatis
plan_id BIGINT FK plans.id DEFAULT 1,
logo VARCHAR(255) NULL,
settings JSON NULL,                 -- preferensi household
created_by BIGINT FK users.id,
timestamps, deleted_at
```

**004_create_subscriptions_table** *(SaaS — struktur ada, bypass dulu)*
```sql
id, household_id BIGINT FK,
plan_id BIGINT FK,
status ENUM('active','expired','cancelled','trial') DEFAULT 'active',
started_at DATE, expired_at DATE NULL,
payment_method VARCHAR(50) NULL,
payment_ref VARCHAR(255) NULL,
timestamps
```
Seed: 1 record untuk household pertama → status active, expired_at NULL

**005_create_household_members_table**
```sql
id, household_id BIGINT FK,
user_id BIGINT FK,
role ENUM('superadmin','admin','viewer') DEFAULT 'admin',
joined_at TIMESTAMP,
invited_by BIGINT NULL FK users.id,
is_active BOOLEAN DEFAULT TRUE
```
*(tidak perlu timestamps — pakai joined_at)*

**006_create_settings_table**
```sql
id, key VARCHAR(100) UNIQUE, value TEXT, group VARCHAR(50)
```
Seed default:
- app_name = "FinanKu" (group: general)
- currency = "IDR" (group: general)
- timezone = "Asia/Jakarta" (group: general)
- date_format = "d F Y" (group: general)
- gemini_api_key = "" (group: api)
- gemini_model = "gemini-2.5-flash" (group: api)
- ocr_daily_limit = "500" (group: api)
- gemini_ocr_used_today = "0" (group: api)
- gemini_reset_date = "" (group: api)
- anomaly_threshold = "2" (group: notification)
- budget_alert_percent = "80" (group: notification)
- superadmin_email = "" (group: general)

**007_create_activity_logs_table**
```sql
id, household_id BIGINT NULL,
user_id BIGINT NULL,
action VARCHAR(100),               -- create/update/delete/login/export/import/ocr
model VARCHAR(100) NULL,
model_id BIGINT NULL,
old_data JSON NULL,
new_data JSON NULL,
ip_address VARCHAR(45) NULL,
user_agent TEXT NULL,
created_at TIMESTAMP
```
*(tidak perlu updated_at — immutable)*

**008_create_notifications_table**
```sql
id, household_id BIGINT FK,
user_id BIGINT FK,
tipe VARCHAR(50),                  -- anomali/budget_alert/duplikat/info/sukses
judul VARCHAR(255),
pesan TEXT,
data JSON NULL,
is_read BOOLEAN DEFAULT FALSE,
created_at TIMESTAMP
```

**009_create_payments_table** *(SaaS — struktur ada, belum aktif)*
```sql
id, household_id BIGINT FK,
subscription_id BIGINT FK,
amount DECIMAL(15,2),
method VARCHAR(50) NULL,
gateway_ref VARCHAR(255) NULL,     -- ID dari Midtrans/Xendit
status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
paid_at TIMESTAMP NULL,
timestamps
```

**010_create_referrals_table** *(SaaS — struktur ada, belum aktif)*
```sql
id, referrer_id BIGINT FK users.id,
referred_id BIGINT FK users.id,
reward_given BOOLEAN DEFAULT FALSE,
created_at TIMESTAMP
```

**011_create_support_tickets_table** *(SaaS — struktur ada, belum aktif)*
```sql
id, user_id BIGINT FK,
household_id BIGINT FK,
subject VARCHAR(255),
message TEXT,
status ENUM('open','in_progress','closed') DEFAULT 'open',
timestamps
```

#### GRUP 2: Core Finance Tables

**012_create_sumber_transaksi_table**
```sql
id, nama ENUM('struk_foto','mutasi_bank','shopee','tiktok_shop','excel','manual')
```
Seed: insert semua 6 nilai

**013_create_toko_table**
```sql
id, household_id BIGINT FK,
nama_asli VARCHAR(255),
nama_normal VARCHAR(255),          -- lowercase slug untuk matching
alias JSON NULL,                   -- array nama alternatif
tipe ENUM('warung','supermarket','online','bank','ewallet','lainnya'),
timestamps, deleted_at
```

**014_create_rekening_table**
```sql
id, household_id BIGINT FK,
nama VARCHAR(255),
tipe ENUM('bank','ewallet','cash','investasi'),
saldo_awal DECIMAL(15,2) DEFAULT 0,
warna VARCHAR(7) NULL,             -- hex color
icon VARCHAR(50) NULL,
is_active BOOLEAN DEFAULT TRUE,
timestamps, deleted_at
```

**015_create_kategori_table**
```sql
id, household_id BIGINT FK,
nama VARCHAR(100),
tipe ENUM('income','outcome'),
parent_id BIGINT NULL FK kategori.id,
icon VARCHAR(50) NULL,
warna VARCHAR(7) NULL,
timestamps
```
Seed default kategori per household baru:
OUTCOME: Makanan & Minuman, Belanja Bulanan, Transportasi,
         Tagihan & Utilitas, Kesehatan, Hiburan, Belanja Online,
         Pendidikan, Pakaian, Lainnya
INCOME:  Gaji/Salary, Freelance, Bisnis, Investasi,
         Cashback/Reward, Transfer Masuk, Lainnya

**016_create_transaksi_table**
```sql
id, household_id BIGINT FK,
toko_id BIGINT NULL FK toko.id,
rekening_id BIGINT NULL FK rekening.id,
kategori_id BIGINT NULL FK kategori.id,
sumber_id BIGINT NULL FK sumber_transaksi.id,
input_by BIGINT FK users.id,
tipe ENUM('income','outcome','transfer') NOT NULL,
tanggal DATE NOT NULL,
total DECIMAL(15,2) NOT NULL,      -- JANGAN float
metode_bayar VARCHAR(50) NULL,     -- tunai/debit/kredit/qris/transfer
catatan TEXT NULL,
tags JSON NULL,                    -- array tag names
file_path VARCHAR(255) NULL,       -- path foto struk utama
attachment_paths JSON NULL,        -- array path attachment tambahan
raw_text TEXT NULL,                -- hasil mentah OCR
hash_dedup VARCHAR(64) NULL,       -- MD5 untuk dedup
sumber_list JSON NULL,             -- ['manual','mutasi_bank'] riwayat sumber
is_detail_lengkap BOOLEAN DEFAULT FALSE,
is_recurring BOOLEAN DEFAULT FALSE,
recurring_id BIGINT NULL,
transfer_ke BIGINT NULL FK rekening.id,
status ENUM('pending','confirmed','duplikat') DEFAULT 'confirmed',
timestamps, deleted_at
```
Index: household_id, tanggal, hash_dedup, toko_id, status

**017_create_transaksi_items_table**
```sql
id, transaksi_id BIGINT FK transaksi.id,
nama_item VARCHAR(255),
qty DECIMAL(10,2) DEFAULT 1,
harga_satuan DECIMAL(15,2),
subtotal DECIMAL(15,2),
is_suggested BOOLEAN DEFAULT FALSE,  -- dari AI atau manual?
is_confirmed BOOLEAN DEFAULT TRUE,
timestamps
```

**018_create_toko_pola_items_table** *(untuk AI suggest)*
```sql
id, toko_id BIGINT FK toko.id,
household_id BIGINT FK,
nama_item VARCHAR(255),
harga_terakhir DECIMAL(15,2),
frekuensi INT DEFAULT 1,           -- berapa kali item ini muncul
updated_at TIMESTAMP
```

**019_create_budget_table**
```sql
id, household_id BIGINT FK,
kategori_id BIGINT FK kategori.id,
bulan TINYINT,                     -- 1-12
tahun YEAR,
jumlah DECIMAL(15,2),
timestamps
```
Unique: household_id + kategori_id + bulan + tahun

**020_create_savings_goals_table**
```sql
id, household_id BIGINT FK,
nama VARCHAR(255),
target_amount DECIMAL(15,2),
current_amount DECIMAL(15,2) DEFAULT 0,
target_date DATE NULL,
icon VARCHAR(50) NULL,
catatan TEXT NULL,
status ENUM('active','achieved','cancelled') DEFAULT 'active',
timestamps, deleted_at
```

**021_create_recurring_transactions_table**
```sql
id, household_id BIGINT FK,
toko_id BIGINT NULL FK,
rekening_id BIGINT NULL FK,
kategori_id BIGINT NULL FK,
nama VARCHAR(255),                 -- "Tagihan Listrik PLN"
tipe ENUM('income','outcome'),
jumlah DECIMAL(15,2),
frekuensi ENUM('harian','mingguan','bulanan','tahunan'),
tanggal_mulai DATE,
tanggal_selesai DATE NULL,
next_run DATE,
last_run DATE NULL,
is_active BOOLEAN DEFAULT TRUE,
timestamps
```

**022_create_tags_table**
```sql
id, household_id BIGINT FK,
nama VARCHAR(100),
warna VARCHAR(7) NULL,
created_at TIMESTAMP
```

**023_create_transaksi_tags_table** *(pivot)*
```sql
transaksi_id BIGINT FK, tag_id BIGINT FK
PRIMARY KEY (transaksi_id, tag_id)
```

---

## TRAITS

### app/Traits/BelongsToHousehold.php
```php
// Global scope otomatis filter household_id untuk semua query
// Boot: tambah addGlobalScope dengan WHERE household_id = current user household
// Boot: tambah creating event → set household_id otomatis dari auth user
// Static method: withoutHouseholdScope() → untuk bypass (superadmin)
// Relasi: household() → belongsTo(Household::class)
```

### app/Traits/HasAuditLog.php
```php
// Boot: tambah created/updated/deleting observer
// Saat created: log action='create', new_data=model->toArray()
// Saat updated: log action='update', old_data=original, new_data=changes
// Saat deleting: log action='delete', old_data=model->toArray()
// Helper: logActivity(action, old, new) → insert ke activity_logs
// Ambil: user_id dari auth(), ip dari request()->ip(), user_agent dari request()
```

### app/Traits/HasSoftDelete.php
```php
// Wrapper SoftDeletes Laravel standar
// Tambah scope: onlyTrashed(), withTrashed()
// Helper method: restore(), forceDelete() (superadmin only)
```

---

## MODELS (Lengkap dengan Relasi, Fillable, Casts)

Semua Model keuangan wajib:
```php
use BelongsToHousehold;
use HasAuditLog;
use SoftDeletes; // kecuali log & pivot
```

### Daftar Model & Relasi Utama:

**User**
- hasMany: HouseholdMember, Transaksi (as input_by), ActivityLog
- belongsToMany: Household (through HouseholdMember)
- Cast: email_verified_at (datetime), last_login_at (datetime)

**Household**
- belongsTo: Plan, User (created_by)
- hasMany: HouseholdMember, Transaksi, Toko, Rekening, Kategori, Budget, SavingsGoal, RecurringTransaction, Notification
- hasOne: Subscription

**HouseholdMember**
- belongsTo: Household, User

**Plan**
- hasMany: Household, Subscription
- Cast: fitur (array)

**Subscription**
- belongsTo: Household, Plan
- Cast: started_at (date), expired_at (date)

**Transaksi**
- use: BelongsToHousehold, HasAuditLog, SoftDeletes
- belongsTo: Toko, Rekening, Kategori, SumberTransaksi, User (input_by)
- hasMany: TransaksiItem
- belongsToMany: Tag (through transaksi_tags)
- Cast: tanggal (date), total (decimal:2), tags (array), attachment_paths (array), sumber_list (array)
- Accessor: totalFormatted → "Rp 1.250.000"
- Accessor: tanggalFormatted → "10 Mei 2026"
- Scope: byTipe($tipe), byPeriod($start, $end), byAnggota($user_id), income(), outcome(), transfer()

**TransaksiItem**
- belongsTo: Transaksi
- Cast: qty (decimal:2), harga_satuan (decimal:2), subtotal (decimal:2)

**Toko**
- use: BelongsToHousehold, HasAuditLog, SoftDeletes
- belongsTo: Household
- hasMany: Transaksi, TokoPola
- Cast: alias (array)
- Scope: byNama($nama) → fuzzy search

**Rekening**
- use: BelongsToHousehold, HasAuditLog, SoftDeletes
- belongsTo: Household
- hasMany: Transaksi
- Accessor: saldoEstimasi → saldo_awal + sum income - sum outcome

**Kategori**
- use: BelongsToHousehold
- belongsTo: Household, Kategori (parent)
- hasMany: Kategori (children), Transaksi, Budget
- Scope: income(), outcome(), parent()

**Budget**
- use: BelongsToHousehold
- belongsTo: Household, Kategori
- Accessor: realisasi → sum transaksi bulan ini di kategori ini
- Accessor: persentase → realisasi/jumlah * 100
- Accessor: isOverBudget → persentase > 100

**SavingsGoal**
- use: BelongsToHousehold, SoftDeletes
- Cast: target_amount (decimal:2), current_amount (decimal:2), target_date (date)
- Accessor: persentaseProgress → current_amount/target_amount * 100
- Accessor: estimasiTercapai → berdasarkan rata-rata tabungan per bulan

**RecurringTransaction**
- use: BelongsToHousehold
- belongsTo: Toko, Rekening, Kategori
- Cast: tanggal_mulai (date), tanggal_selesai (date), next_run (date), last_run (date)

**TokoPola**
- belongsTo: Toko, Household
- Cast: harga_terakhir (decimal:2)

**Tag**
- use: BelongsToHousehold
- belongsToMany: Transaksi

**Setting**
- Static method: get($key, $default = null)
- Static method: set($key, $value)
- Static method: group($group) → array

**ActivityLog**
- belongsTo: User, Household
- Cast: old_data (array), new_data (array), created_at (datetime)

**Notification**
- belongsTo: User, Household
- Scope: unread(), forUser($user_id)

**Payment** *(SaaS)*
- belongsTo: Household, Subscription

---

## SERVICES

### GeminiService.php
**Lokasi:** app/Services/GeminiService.php

**Constructor:**
- Baca GEMINI_API_KEY dari .env atau Setting::get('gemini_api_key')
- Baca GEMINI_MODEL dari .env
- Base URL: https://generativelanguage.googleapis.com/v1beta/models

**Methods:**

```php
public function ocrAndExtract(string $base64Image, string $mimeType): array
// Kirim gambar ke Gemini Vision API
// System prompt: "Kamu adalah asisten ekstraksi data struk belanja."
// User prompt: "Ekstrak data dari struk/screenshot ini dalam format JSON:
//   {tanggal, nama_toko, tipe_toko, tipe_transaksi(income/outcome),
//    items:[{nama_item,qty,harga_satuan,subtotal}],
//    total, metode_bayar, catatan}
//   Jika tidak ditemukan, isi dengan null.
//   Tanggal format: YYYY-MM-DD.
//   Semua angka tanpa titik/koma pemisah."
// Cache key: MD5($base64Image) — cache 24 jam
// Return: array hasil parse JSON
// Throw: GeminiException jika API error atau response tidak valid

public function suggestDetail(string $nama_toko, float $total, array $history): array
// $history = array of {tanggal, items:[{nama_item,qty,harga_satuan,subtotal}], total}
// Prompt: "Pengguna beli di [toko] total Rp [total].
//   Berdasarkan history [N] transaksi sebelumnya: [history]
//   Suggest breakdown detail dalam JSON array items."
// Cache key: MD5(nama_toko . $total . json_encode($history))
// Return: array items atau [] jika tidak bisa suggest

public function generateInsight(array $data): string
// $data = {bulan, tahun, total_income, total_outcome, cashflow,
//          saving_rate, top_kategori[], top_toko[], trend_vs_bulan_lalu}
// Prompt: "Analisis data keuangan keluarga bulan [bulan] [tahun].
//   Berikan insight dalam Bahasa Indonesia yang ramah, 3-5 poin penting,
//   dan 2-3 saran actionable. Maksimal 300 kata."
// Cache key: MD5(json_encode($data)) — cache 24 jam
// Return: string insight

public function detectAnomaly(array $transaksi, array $rata_rata): array
// Prompt: "Transaksi baru: [transaksi]. Rata-rata historis: [rata_rata].
//   Apakah ini anomali? Response JSON: {is_anomaly, alasan, severity(low/mid/high)}"
// Return: array {is_anomaly:bool, alasan:string, severity:string}

private function callAPI(array $payload): array
// Core HTTP call ke Gemini API menggunakan Laravel Http facade
// Timeout: 30 detik
// Retry: 2x jika timeout
// Log setiap call ke activity_logs (action='gemini_api_call')
// Increment counter gemini_ocr_used_today di settings
// Throw GeminiException jika response error

private function checkDailyLimit(): void
// Cek apakah sudah mencapai limit harian
// Reset counter jika sudah ganti hari
// Throw GeminiLimitException jika limit tercapai

private function parseJsonResponse(string $text): array
// Bersihkan response dari markdown code blocks (```json ... ```)
// Parse JSON
// Throw jika tidak valid JSON
```

**Exceptions:**
- app/Exceptions/GeminiException.php
- app/Exceptions/GeminiLimitException.php

---

### DedupService.php
**Lokasi:** app/Services/DedupService.php

```php
public function generateHash(string $tanggal, float $total, int $toko_id): string
// Return MD5(tanggal . round($total, 0) . $toko_id)

public function checkDuplicate(string $hash, int $household_id): ?Transaksi
// Cari transaksi dengan hash_dedup = $hash AND household_id = $household_id
// Return Transaksi atau null

public function checkSoftDuplicate(string $tanggal, float $total, int $household_id, float $toleransi = 500): ?Transaksi
// Cari transaksi dengan tanggal sama, household sama
// total dalam range: ($total - $toleransi) sampai ($total + $toleransi)
// Return Transaksi pertama yang ditemukan atau null

public function checkImportRow(string $tanggal, float $total, string $nama_toko_raw, int $household_id): array
// Step 1: findTokoByName($nama_toko_raw, $household_id) → dapat toko_id
// Step 2: jika tidak ketemu toko → return ['status' => 'baru', 'toko_baru' => true]
// Step 3: generateHash → checkDuplicate
// Step 4: jika tidak exact match → checkSoftDuplicate
// Step 5: fuzzy match nama toko dengan similar_text()
//   >= 80% → status: 'sudah_ada'
//   60-79% → status: 'mirip'
//   < 60%  → status: 'baru'
// Return: {status, transaksi_id:?, similarity:?, toko_id:?}

public function mergeSumber(int $transaksi_id, string $sumber_baru): void
// Ambil transaksi, ambil sumber_list (JSON)
// Tambah sumber_baru jika belum ada
// Update kolom sumber_list

private function findTokoByName(string $nama_raw, int $household_id): ?Toko
// Cari di toko.nama_normal, toko.nama_asli, dan toko.alias
// Gunakan similar_text() untuk fuzzy match
// Return Toko dengan similarity tertinggi (minimum 60%)
```

---

### OCRService.php
**Lokasi:** app/Services/OCRService.php

```php
public function validateFile(UploadedFile $file): array
// Cek: extension (jpg,jpeg,png,webp), size (max 5MB)
// Return: {valid:bool, error:string|null}

public function compressAndSave(UploadedFile $file, string $folder = 'struk'): string
// Compress via ImageService::compress()
// Simpan ke storage/app/public/{folder}/{uuid}.jpg
// Return: path relatif

public function toBase64(string $path): string
// Baca file dari storage, encode base64
// Return: base64 string

public function getMimeType(string $path): string
// Return mime type file (image/jpeg, image/png, image/webp)

public function processUpload(UploadedFile $file): array
// Validasi → compress → simpan → convert base64
// Return: {path, base64, mime_type, original_name, size_kb}
```

---

### ImageService.php
**Lokasi:** app/Services/ImageService.php

```php
public function compress(UploadedFile $file, int $maxWidth = 800, int $quality = 80): string
// Pakai Intervention Image
// Resize jika lebar > maxWidth (maintain aspect ratio)
// Encode ke JPEG dengan quality
// Simpan ke temp path
// Return: temp path

public function generateThumbnail(string $path, int $width = 200): string
// Buat thumbnail dari file yang sudah disimpan
// Return: path thumbnail
```

---

### BankMutasiParser/ (Folder)

**Interface:** app/Services/BankMutasiParser/BankParserInterface.php
```php
interface BankParserInterface {
    public function detect(array $headers): bool;
    public function parse(array $rows): array;
    // Output standar per row:
    // {tanggal:Y-m-d, nama_toko_raw:string, total:float,
    //  tipe:income|outcome, keterangan:string, sumber:'mutasi_bank'}
}
```

**Implementasi:**
- BCAParser.php → kolom: Tanggal, Keterangan, Cabang, Jumlah, Saldo
- MandiriParser.php → kolom: Tanggal Transaksi, Deskripsi, Nominal, Tipe
- BNIParser.php → kolom: Tanggal, Keterangan, Mutasi, Saldo
- BSIParser.php → kolom: Tanggal, Uraian, Debet, Kredit, Saldo
- GenericParser.php → fallback, deteksi kolom otomatis

**Catatan parsing:**
- Kolom KREDIT/MASUK → tipe: income
- Kolom DEBET/KELUAR → tipe: outcome
- Keterangan mengandung "TRF/TRANSFER KE" → tipe: transfer
- Hapus karakter: Rp, titik, koma dari angka sebelum parse

---

### MarketplaceParser/ (Folder)

**ShopeeParser.php** → app/Services/MarketplaceParser/ShopeeParser.php
- Parse CSV export Shopee atau screenshot via Gemini Vision
- Output standar sama dengan BankParser

**TiktokShopParser.php** → app/Services/MarketplaceParser/TiktokShopParser.php
- Parse CSV export TikTok Shop atau screenshot via Gemini Vision

---

### BankMutasiImportService.php
**Lokasi:** app/Services/BankMutasiImportService.php

```php
public function detectFormat(UploadedFile $file): string
// Baca baris pertama (header) dari Excel
// Coba setiap parser: detect($headers)
// Return: nama bank ('bca','mandiri','bni','bsi','generic')

public function parseFile(UploadedFile $file): array
// Detect format → gunakan parser yang sesuai
// Return: array of standardized rows

public function processAllRows(array $rows, int $household_id): array
// Per baris: jalankan DedupService::checkImportRow()
// Kelompokkan: {baru:[], sudah_ada:[], mirip:[], total:int}
// Return: hasil kelompok untuk preview

public function confirmImport(array $selected_rows, int $household_id): array
// Hanya proses baris dengan status 'baru' yang dipilih user
// Untuk setiap baris:
//   → findOrCreateToko() via TokoPolaService
//   → Buat Transaksi baru
//   → Set hash_dedup
//   → Set sumber_list: ['mutasi_bank']
// Return: {imported:int, errors:[]}
```

---

### LaporanService.php
**Lokasi:** app/Services/LaporanService.php

```php
public function harian(string $tanggal, int $household_id): array
// Return: {tanggal, transaksi[], total_income, total_outcome,
//          cashflow, breakdown_toko[], breakdown_kategori[]}

public function mingguan(string $tanggal_awal, int $household_id): array
// 7 hari dari tanggal_awal
// Return: {periode, per_hari[], total_income, total_outcome,
//          cashflow, saving_rate, top_toko[], top_kategori[]}

public function bulanan(int $bulan, int $tahun, int $household_id): array
// Return: {bulan, tahun, total_income, total_outcome, cashflow,
//          saving_rate, per_hari[], per_kategori[], per_toko[],
//          per_anggota[], vs_bulan_lalu{}, top_items[]}

public function perAnggota(int $user_id, int $bulan, int $tahun, int $household_id): array
// Transaksi yang input_by = $user_id
// Return: format sama dengan bulanan

public function trendEnamBulan(int $household_id): array
// 6 bulan terakhir
// Return: [{bulan, tahun, income, outcome, cashflow}]

public function netCashflow(int $bulan, int $tahun, int $household_id): float
// Return: total income - total outcome (EXCLUDE transfer)

public function savingRate(int $bulan, int $tahun, int $household_id): float
// Return: (income - outcome) / income * 100
// Return 0 jika income = 0
```

---

### BudgetService.php
**Lokasi:** app/Services/BudgetService.php

```php
public function getRealisasi(int $kategori_id, int $bulan, int $tahun, int $household_id): float
// Sum total transaksi outcome dengan kategori_id tersebut di bulan/tahun

public function getPersentase(int $kategori_id, int $bulan, int $tahun, int $household_id): float
// realisasi / budget * 100

public function getSummaryBulanan(int $bulan, int $tahun, int $household_id): array
// Semua budget bulan ini dengan realisasi masing-masing
// Return: [{kategori, budget, realisasi, persentase, status:ok|warning|over}]

public function checkAlert(int $household_id): array
// Cek semua budget bulan berjalan
// Return kategori yang persentase > Setting::get('budget_alert_percent')
// Trigger NotificationService::send() untuk setiap yang over threshold
```

---

### InsightService.php
**Lokasi:** app/Services/InsightService.php

```php
public function generateMonthlyInsight(int $bulan, int $tahun, int $household_id): string
// Cache key: "insight_{household_id}_{bulan}_{tahun}" — cache 24 jam
// Kumpulkan data via LaporanService::bulanan()
// Kirim ke GeminiService::generateInsight()
// Simpan ke cache
// Return: string insight dalam Bahasa Indonesia
```

---

### AnomalyDetectionService.php
**Lokasi:** app/Services/AnomalyDetectionService.php

```php
public function checkNewTransaction(Transaksi $transaksi): ?array
// Ambil rata-rata transaksi di toko yang sama (30 hari terakhir)
// Jika total > (rata_rata * threshold) → kirim ke GeminiService::detectAnomaly()
// Jika is_anomaly = true → NotificationService::sendToAll()
// Return: array anomaly info atau null

public function getRataRata(int $toko_id, int $household_id, int $days = 30): array
// Return: {rata_total, count_transaksi, min_total, max_total}
```

---

### ExportService.php
**Lokasi:** app/Services/ExportService.php

```php
public function toExcel(array $data, string $tipe): BinaryFileResponse
// $tipe: 'harian'|'mingguan'|'bulanan'
// Format Excel profesional dengan header, summary, detail
// Return download response

public function toPDF(array $data, string $view): BinaryFileResponse
// Render view Blade → DomPDF
// Return download response

public function toZIP(int $household_id): BinaryFileResponse
// Kumpulkan: semua laporan 12 bulan terakhir (Excel) + semua foto struk
// Zip semua file
// Return download response
// Hapus temp file setelah download
```

---

### NotificationService.php
**Lokasi:** app/Services/NotificationService.php

```php
public function send(int $household_id, int $user_id, string $tipe, string $judul, string $pesan, array $data = []): void
// Insert ke tabel notifications

public function sendToAll(int $household_id, string $tipe, string $judul, string $pesan, array $data = []): void
// Kirim ke semua anggota aktif household

public function markAsRead(int $notification_id, int $user_id): void
// Update is_read = true (pastikan milik user ini)

public function markAllAsRead(int $user_id, int $household_id): void
// Update semua is_read = true milik user ini

public function getUnread(int $user_id, int $household_id): Collection
// Return notifikasi belum dibaca, order by created_at DESC

public function getUnreadCount(int $user_id, int $household_id): int
```

---

### PlanLimitService.php
**Lokasi:** app/Services/PlanLimitService.php

```php
// FASE INTERNAL: semua method return TRUE (bypass)
// Struktur lengkap untuk diaktifkan saat SaaS launch

public function canAddTransaksi(int $household_id): bool { return true; }
public function canUseOCR(int $household_id): bool { return true; }
public function canAddAnggota(int $household_id): bool { return true; }
public function getUsage(int $household_id): array
// Return usage stats (untuk superadmin dashboard)
// {transaksi_bulan_ini, ocr_bulan_ini, jumlah_anggota, plan_name}
```

---

### TokoPolaService.php
**Lokasi:** app/Services/TokoPolaService.php

```php
public function findOrCreateToko(string $nama_raw, int $household_id): Toko
// Cari toko dengan nama mirip (similar_text >= 80%)
// Jika ketemu → update alias, return toko existing
// Jika tidak → create toko baru dengan nama_normal = Str::slug($nama_raw)

public function getHistory(int $toko_id, int $limit = 5): array
// Ambil $limit transaksi terakhir toko ini dengan items
// Return: array of {tanggal, total, items:[{nama_item,qty,harga_satuan,subtotal}]}

public function updatePola(int $toko_id, int $household_id, array $items): void
// Per item: cari di toko_pola_items
// Jika ada → update harga_terakhir, increment frekuensi
// Jika tidak → create baru

public function getSuggest(int $toko_id, float $total_baru, int $household_id): array
// Ambil history via getHistory()
// Jika history kosong → return []
// Kirim ke GeminiService::suggestDetail()
// Return: array items dengan is_suggested = true
```

---

### ReferralService.php *(SaaS — struktur ada, belum aktif)*
**Lokasi:** app/Services/ReferralService.php

```php
public function generateCode(int $user_id): string { /* generate unique code */ }
public function processReferral(string $code, int $new_user_id): void { /* placeholder */ }
public function giveReward(int $referral_id): void { /* placeholder */ }
```

---

## MIDDLEWARE

### app/Http/Middleware/HouseholdMiddleware.php
- Cek apakah user sudah punya household (via HouseholdMember)
- Jika belum → redirect ke /onboarding
- Jika punya → set session 'active_household_id'
- Share data household ke semua view: view()->share('activeHousehold', ...)

### app/Http/Middleware/RoleMiddleware.php
- Parameter: $role (superadmin|admin|viewer)
- Cek role user di household aktif
- Abort 403 dengan pesan Bahasa Indonesia jika tidak punya permission
- Superadmin bisa akses semua yang admin bisa

### app/Http/Middleware/CheckPlanLimit.php
- Parameter: $feature (transaksi|ocr|anggota)
- FASE INTERNAL: selalu pass → return $next($request)
- Struktur sudah siap untuk aktivasi

### app/Http/Middleware/LogActivity.php
- Skip: GET requests, asset requests, cron endpoints
- Log: POST/PUT/PATCH/DELETE ke activity_logs
- Data: user_id, action (dari method HTTP), url, ip, user_agent

### app/Http/Middleware/CronSecret.php
- Cek header X-Cron-Secret atau query param ?secret=
- Bandingkan dengan CRON_SECRET_KEY di .env
- Abort 403 jika tidak cocok

### app/Http/Middleware/SuperadminGlobal.php
- Cek apakah email user = SUPERADMIN_EMAIL di .env
- Abort 403 jika bukan superadmin
- Superadmin ini berbeda dengan role 'superadmin' di household

---

## CONTROLLERS

### Auth/RegisterController.php
- Form: nama, email, password, password_confirmation
- Setelah register:
  - Generate referral_code unik
  - Jika ada ?ref=CODE → simpan referred_by
  - Redirect ke /onboarding

### Auth/LoginController.php
- Setelah login: update last_login_at
- Log ke activity_logs: action='login'
- Redirect ke /dashboard

### OnboardingController.php
Step-by-step dengan session tracking:

**Step 1 - Household:**
- Form: nama household
- Buat household baru → generate slug, kode_invite
- Buat subscription dengan plan 'internal'
- Buat HouseholdMember dengan role 'superadmin'
- Set session active_household_id

**Step 2 - Rekening:**
- Form: nama, tipe, saldo_awal
- Minimal 1 rekening wajib
- Bisa tambah lebih dari 1
- Tombol Skip (rekening bisa ditambah nanti)

**Step 3 - Budget (opsional):**
- Tampil daftar kategori default
- User set budget per kategori
- Tombol Skip

**Step 4 - Recurring (opsional):**
- Form: nama tagihan, jumlah, frekuensi, tanggal
- Tombol Skip

**Step 5 - Invite (opsional):**
- Tampil kode invite household
- Instruksi cara share ke anggota keluarga
- Tombol Skip / Selesai

**Selesai:** Redirect /dashboard dengan flash message selamat datang

### DashboardController.php
```php
public function index()
// Data yang dikumpulkan:
// - stat cards: income, outcome, cashflow, saving rate bulan ini
// - tren 6 bulan: LaporanService::trendEnamBulan()
// - budget summary: BudgetService::getSummaryBulanan()
// - savings goals: semua active goals
// - 5 transaksi terakhir
// - upcoming recurring (next_run dalam 7 hari)
// - unread notifications
// - AI insight (InsightService, cache 24 jam)
// - breakdown per anggota
// - budget alerts (BudgetService::checkAlert())
```

### TransaksiController.php
```php
index()        // List dengan filter lengkap + pagination 15
create()       // Form dengan 4 tab
store()        // Simpan manual: dedup check → anomaly check → update pola → save
show($id)      // Detail + items + audit log transaksi ini
edit($id)      // Form edit (cek permission: admin edit milik sendiri saja)
update($id)    // Update + audit log
destroy($id)   // Soft delete (superadmin household only)
restore($id)   // Restore soft deleted (superadmin household only)

uploadStruk()  // POST: upload gambar → OCRService → GeminiService → return JSON hasil
               // BELUM simpan ke DB, hanya return extracted data
               // Cek PlanLimitService::canUseOCR() dulu

confirmOCR()   // POST: terima hasil OCR yang sudah diedit user → simpan ke DB
               // Jalankan dedup check, anomaly check, update pola toko

getSuggest()   // GET: ?toko_id=&total= → TokoPolaService::getSuggest() → return JSON

confirmSuggest() // POST: simpan items dari suggest yang dikonfirmasi user
                 // Update toko_pola_items
                 // Set is_detail_lengkap = true
```

### ImportController.php
```php
showForm()     // Form upload: pilih tipe (bank/shopee/tiktok) + upload file

preview()      // POST: parse file → DedupService per baris → return view preview tabel
               // BELUM simpan ke DB sama sekali
               // Tampilkan: baru(✅), sudah_ada(⚠️), mirip(❓) per baris

confirmImport() // POST: terima array selected_row_ids
                // BankMutasiImportService::confirmImport()
                // Flash: "X transaksi berhasil diimport, Y diskip"
                // Redirect ke /transaksi
```

### LaporanController.php
```php
index()                    // Pilih jenis laporan
harian(Request $request)   // ?tanggal=
mingguan(Request $request) // ?tanggal_awal=
bulanan(Request $request)  // ?bulan=&tahun=
exportExcel(Request $request) // ?tipe=&bulan=&tahun=
exportPDF(Request $request)
exportZIP()                // Semua data + foto struk
```

### BudgetController.php
```php
index()    // List budget bulan ini vs realisasi
store()    // Set budget: {kategori_id, bulan, tahun, jumlah}
           // Upsert: update jika sudah ada, insert jika baru
update()   // Update jumlah budget
destroy()  // Hapus budget kategori ini
summary()  // GET JSON: summary untuk chart di dashboard
```

### SavingsGoalController.php
```php
index()      // List semua goals dengan progress bar
store()      // Buat goal baru
update()     // Edit goal
addFund()    // POST: tambah dana ke goal, update current_amount
achieve()    // POST: tandai sebagai achieved
destroy()    // Soft delete
```

### RecurringController.php
```php
index()      // List semua recurring transactions
store()      // Buat baru, set next_run = tanggal_mulai
update()     // Edit
toggle($id)  // Aktif/nonaktif
destroy()    // Hapus
processAll() // Dijalankan oleh CronController::recurring()
             // Proses semua recurring dengan next_run <= hari ini
             // Buat Transaksi baru per recurring yang jatuh tempo
             // Update last_run dan next_run
             // Kirim notifikasi ke anggota
```

### HouseholdController.php
```php
index()              // Dashboard household: info + anggota
members()            // List anggota dengan role
invite()             // POST: generate kode invite baru (expire 7 hari)
joinForm()           // GET: form masukkan kode invite
join(Request $req)   // POST: gabung household via kode
updateRole()         // POST: ubah role anggota (superadmin only)
removeMember()       // POST: keluarkan anggota (superadmin only)
regenerateInvite()   // POST: generate ulang kode invite
```

### RekenungController.php
```php
index()    // List rekening + estimasi saldo per rekening
store()    // Tambah rekening
update()   // Edit
destroy()  // Soft delete (cek: tidak ada transaksi yang pakai rekening ini)
```

### KategoriController.php
```php
index()    // List income + outcome (tree view jika ada parent)
store()    // Tambah
update()   // Edit
destroy()  // Hapus (cek: tidak ada transaksi)
```

### TagController.php
```php
index()    // List tags
store()    // Buat tag
destroy()  // Hapus
```

### SettingsController.php
```php
index()              // Halaman settings utama
updateProfile()      // Nama, avatar, timezone preference
updatePassword()     // Ganti password
updateHousehold()    // Nama household, logo
updateNotification() // Threshold budget alert, anomali on/off
updateGeminiKey()    // Update API key Gemini (superadmin household)
getApiUsage()        // GET JSON: sisa quota Gemini hari ini
```

### NotificationController.php
```php
index()        // List semua notifikasi dengan pagination
markRead($id)  // Tandai 1 notifikasi terbaca
markAllRead()  // Tandai semua terbaca
```

### SuperadminController.php
*(Middleware: SuperadminGlobal — hanya untuk owner aplikasi)*

```php
dashboard()      // Stats: total households, users, transaksi hari ini,
                 // Gemini API usage, storage usage, error log terbaru
households()     // List semua household dengan usage detail
showHousehold()  // Detail 1 household: anggota, transaksi, usage
users()          // List semua user
suspendUser()    // Suspend/unsuspend user
activityLogs()   // Semua activity log (bisa filter by household/user/action)
settings()       // Kelola settings global (app_name, API keys, dll)
plans()          // CRUD plans (untuk persiapan SaaS)
health()         // System health check:
                 // - DB connection test
                 // - Gemini API test ping
                 // - Storage usage (disk_free_space)
                 // - PHP version + extensions
                 // - Gemini quota hari ini
                 // - Last 10 errors dari log
```

### CronController.php
*(Middleware: CronSecret)*

```php
recurring()   // GET /cron/recurring
              // Jalankan RecurringController::processAll()
              // Log ke activity_logs: action='cron_recurring'
              // Return JSON: {success, processed, errors}

reminder()    // GET /cron/reminder
              // BudgetService::checkAlert() → kirim notifikasi
              // Log ke activity_logs: action='cron_reminder'
              // Return JSON: {success, alerts_sent}
```

### Api/V1/TransaksiController.php
*(Middleware: auth:sanctum)*

```php
index()    // GET /api/v1/transaksi → paginated list
store()    // POST /api/v1/transaksi → create
show($id)  // GET /api/v1/transaksi/{id}
```

### Api/V1/DashboardController.php
```php
summary()  // GET /api/v1/dashboard/summary → stats bulan ini
```

### Api/V1/LaporanController.php
```php
bulanan()  // GET /api/v1/laporan/bulanan?bulan=&tahun=
```

---

## FORM REQUESTS

Buat Form Request untuk semua input:
- StoreTransaksiRequest → validasi manual input
- UpdateTransaksiRequest
- StoreRekenungRequest
- StoreBudgetRequest
- StoreSavingsGoalRequest
- StoreRecurringRequest
- UpdateProfileRequest
- UpdatePasswordRequest
- UpdateHouseholdRequest
- JoinHouseholdRequest
- ImportFileRequest → validasi file upload (type + size)

---

## ROUTES

### routes/web.php

```php
// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', ...)->name('password.request');
    Route::post('/forgot-password', ...)->name('password.email');
    Route::get('/reset-password/{token}', ...)->name('password.reset');
    Route::post('/reset-password', ...)->name('password.update');
});

// Auth required (termasuk onboarding — belum perlu household)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding/step/{step}', [OnboardingController::class, 'processStep'])->name('onboarding.step');
    Route::get('/household/join', [HouseholdController::class, 'joinForm'])->name('household.join.form');
    Route::post('/household/join', [HouseholdController::class, 'join'])->name('household.join');
});

// Auth + Household required
Route::middleware(['auth', 'verified', 'household'])->group(function () {

    Route::get('/', function() { return redirect()->route('dashboard'); });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi
    Route::resource('transaksi', TransaksiController::class);
    Route::post('/transaksi/upload-struk', [TransaksiController::class, 'uploadStruk'])->name('transaksi.upload-struk');
    Route::post('/transaksi/confirm-ocr', [TransaksiController::class, 'confirmOCR'])->name('transaksi.confirm-ocr');
    Route::get('/transaksi/suggest', [TransaksiController::class, 'getSuggest'])->name('transaksi.suggest');
    Route::post('/transaksi/confirm-suggest/{id}', [TransaksiController::class, 'confirmSuggest'])->name('transaksi.confirm-suggest');
    Route::post('/transaksi/{id}/restore', [TransaksiController::class, 'restore'])->name('transaksi.restore')->middleware('role:superadmin');

    // Import
    Route::get('/import', [ImportController::class, 'showForm'])->name('import.form');
    Route::post('/import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/confirm', [ImportController::class, 'confirmImport'])->name('import.confirm');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/mingguan', [LaporanController::class, 'mingguan'])->name('laporan.mingguan');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPDF'])->name('laporan.export.pdf');
    Route::get('/laporan/export/zip', [LaporanController::class, 'exportZIP'])->name('laporan.export.zip');

    // Budget
    Route::resource('budget', BudgetController::class)->except(['show']);
    Route::get('/budget/summary', [BudgetController::class, 'summary'])->name('budget.summary');

    // Savings Goals
    Route::resource('savings-goals', SavingsGoalController::class);
    Route::post('/savings-goals/{id}/add-fund', [SavingsGoalController::class, 'addFund'])->name('savings-goals.add-fund');
    Route::post('/savings-goals/{id}/achieve', [SavingsGoalController::class, 'achieve'])->name('savings-goals.achieve');

    // Recurring
    Route::resource('recurring', RecurringController::class);
    Route::post('/recurring/{id}/toggle', [RecurringController::class, 'toggle'])->name('recurring.toggle');

    // Rekening
    Route::resource('rekening', RekenungController::class)->except(['show']);

    // Kategori
    Route::resource('kategori', KategoriController::class)->except(['show']);

    // Tags
    Route::resource('tags', TagController::class)->except(['show','edit','update']);

    // Household Management
    Route::get('/household', [HouseholdController::class, 'index'])->name('household.index');
    Route::get('/household/members', [HouseholdController::class, 'members'])->name('household.members');
    Route::post('/household/invite', [HouseholdController::class, 'invite'])->name('household.invite');
    Route::post('/household/member/{id}/role', [HouseholdController::class, 'updateRole'])->name('household.member.role')->middleware('role:superadmin');
    Route::delete('/household/member/{id}', [HouseholdController::class, 'removeMember'])->name('household.member.remove')->middleware('role:superadmin');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/household', [SettingsController::class, 'updateHousehold'])->name('settings.household');
    Route::post('/settings/notification', [SettingsController::class, 'updateNotification'])->name('settings.notification');
    Route::post('/settings/gemini', [SettingsController::class, 'updateGeminiKey'])->name('settings.gemini');
    Route::get('/settings/api-usage', [SettingsController::class, 'getApiUsage'])->name('settings.api-usage');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Superadmin (owner aplikasi)
Route::middleware(['auth', 'superadmin-global'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('dashboard');
    Route::get('/households', [SuperadminController::class, 'households'])->name('households');
    Route::get('/households/{id}', [SuperadminController::class, 'showHousehold'])->name('households.show');
    Route::get('/users', [SuperadminController::class, 'users'])->name('users');
    Route::post('/users/{id}/suspend', [SuperadminController::class, 'suspendUser'])->name('users.suspend');
    Route::get('/logs', [SuperadminController::class, 'activityLogs'])->name('logs');
    Route::get('/settings', [SuperadminController::class, 'settings'])->name('settings');
    Route::post('/settings', [SuperadminController::class, 'updateSettings'])->name('settings.update');
    Route::resource('/plans', SuperadminPlanController::class)->name('plans');
    Route::get('/health', [SuperadminController::class, 'health'])->name('health');
});

// Cron Jobs (dipanggil oleh cron-job.org)
Route::middleware('cron-secret')->prefix('cron')->name('cron.')->group(function () {
    Route::get('/recurring', [CronController::class, 'recurring'])->name('recurring');
    Route::get('/reminder', [CronController::class, 'reminder'])->name('reminder');
});
```

### routes/api.php
```php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/transaksi', [Api\V1\TransaksiController::class, 'index']);
    Route::post('/transaksi', [Api\V1\TransaksiController::class, 'store']);
    Route::get('/transaksi/{id}', [Api\V1\TransaksiController::class, 'show']);
    Route::get('/dashboard/summary', [Api\V1\DashboardController::class, 'summary']);
    Route::get('/laporan/bulanan', [Api\V1\LaporanController::class, 'bulanan']);
});
```

---

## VIEWS

### Layout Utama: resources/views/layouts/app.blade.php

Sidebar navigasi (collapsible di mobile):
```
Logo FinanKu
─────────────
📊 Dashboard
💳 Transaksi
  └ Semua Transaksi
  └ Input Baru
  └ Import Mutasi
📈 Laporan
  └ Harian
  └ Mingguan
  └ Bulanan
💰 Budget
🎯 Target Tabungan
🔄 Tagihan Rutin
🏦 Rekening
🏷️ Kategori
👥 Household
⚙️ Pengaturan
─────────────
[Superadmin] ← hanya jika superadmin global
```

Topbar:
- Nama household aktif
- Bell icon (badge unread count, polling setiap 60 detik)
- Avatar user + dropdown (profil, logout)
- Dark mode toggle

Footer: versi aplikasi, copyright

### Komponen Reusable (resources/views/components/)

**stat-card.blade.php:**
Props: title, value, subtitle, color, icon, trend(up/down/neutral)

**transaction-row.blade.php:**
Props: transaksi object
Tampil: tanggal, toko/sumber, total (merah/hijau), badge tipe, badge sumber, status detail

**suggest-detail.blade.php:**
Props: items[], total_suggest, total_transaksi
Tampil:
- Header "💡 Saran AI berdasarkan history"
- Tabel items dengan checkbox per item
- Total suggest vs total transaksi
- Warning jika tidak sama
- Tombol: [✅ Pakai Semua] [✏️ Edit] [🔄 Reset]

**duplikat-modal.blade.php:**
Props: transaksi_existing, transaksi_baru
Tampil perbandingan side-by-side
Tombol: [Ya, Ini Duplikat - Abaikan] [Tidak, Simpan Sebagai Baru]

**budget-progress.blade.php:**
Props: kategori, budget, realisasi, persentase
Progress bar dengan warna: hijau(<70%), kuning(70-90%), merah(>90%)

**empty-state.blade.php:**
Props: icon, title, description, cta_text, cta_url
Tampil ilustrasi SVG + CTA button

**confirm-modal.blade.php:**
Alpine.js modal konfirmasi hapus/aksi berbahaya

**file-upload.blade.php:**
Drag & drop area dengan preview gambar
Gunakan Alpine.js untuk preview

**notification-bell.blade.php:**
Bell icon dengan badge
Dropdown list 5 notifikasi terbaru
Link "Lihat Semua"
Polling setiap 60 detik via fetch API

### Halaman Utama

**dashboard.blade.php:**
- Grid 4 stat cards (income, outcome, cashflow, saving rate)
- Chart tren 6 bulan (Chart.js line chart)
- Budget vs realisasi (progress bars per kategori)
- Savings goals cards (2 kolom)
- Tabel 5 transaksi terakhir
- Upcoming recurring (7 hari ke depan)
- AI Insight card (collapsible, refresh manual)
- Breakdown per anggota (tabel sederhana)
- Alert box jika ada budget yang over threshold

**transaksi/index.blade.php:**
Filter bar: tanggal dari-sampai, toko, kategori, tipe, anggota, tag, sumber, status
Tabel dengan: checkbox, tanggal, toko, kategori, total, tipe badge, sumber badge, detail badge, aksi
Pagination 15 per halaman
Summary bar: total income, total outcome, cashflow dari hasil filter

**transaksi/create.blade.php:**
4 Tab dengan Alpine.js:

Tab 1 — Scan Struk:
- Drag & drop area / button kamera (input accept="image/*" capture="camera")
- Preview gambar
- Button "Proses OCR" → fetch POST /transaksi/upload-struk
- Loading spinner
- Hasil OCR: form pre-filled yang bisa diedit
- Jika toko dikenali → tampil komponen suggest-detail

Tab 2 — Import Mutasi Bank:
- Upload Excel
- Tampil format bank yang terdeteksi
- Redirect ke halaman preview import

Tab 3 — Marketplace:
- Pilih: Shopee / TikTok Shop
- Upload CSV atau screenshot
- Proses → redirect preview

Tab 4 — Manual:
- Form: tipe (income/outcome/transfer), tanggal, toko (autocomplete)
- Rekening, kategori, metode bayar, catatan, tags
- Jika toko dipilih dan punya history → auto fetch suggest detail
- Section items: tabel dinamis (Alpine.js) tambah/hapus baris
- Validasi real-time: total items harus = total transaksi
- Tombol submit

**transaksi/show.blade.php:**
- Info header: toko, tanggal, total, tipe, sumber, metode bayar
- Foto struk (klik untuk lightbox)
- Attachment tambahan
- Tabel items detail
- Catatan & tags
- Timeline audit log transaksi ini (created, updated, etc)
- Tombol edit (jika admin) / hapus (jika superadmin)

**import/preview.blade.php:**
- Info: nama file, format bank terdeteksi, total baris
- Summary badges: ✅ Baru (N), ⚠️ Sudah Ada (N), ❓ Perlu Konfirmasi (N)
- Tabel preview:
  - Checkbox per baris
  - Badge status per baris
  - Baris ⚠️: disabled, abu-abu
  - Baris ❓: show detail transaksi existing di bawahnya (collapsible)
- Tombol: [Pilih Semua ✅] [Import yang Dipilih] [Batal]

**laporan/bulanan.blade.php:**
- Filter: pilih bulan/tahun
- Stat cards: income, outcome, cashflow, saving rate
- Chart bar: income vs outcome per hari dalam bulan (Chart.js)
- Chart pie: breakdown per kategori outcome (Chart.js)
- Tabel: per toko dengan total dan jumlah transaksi
- Tabel: per anggota dengan total
- Filter: [Semua Anggota ▼] [Nama Anggota...]
- Tombol export: [📊 Excel] [📄 PDF] [📦 ZIP Semua Data]

**budget/index.blade.php:**
- Header: Bulan ini (filter bulan/tahun)
- Tombol: Set Budget Bulan Ini
- Grid budget cards per kategori:
  - Nama kategori + icon
  - Progress bar (hijau/kuning/merah)
  - Budget: Rp X | Realisasi: Rp Y | Sisa: Rp Z
  - Persentase
- Total summary di bawah

**savings-goals/index.blade.php:**
- Grid goals cards:
  - Nama + icon
  - Progress bar
  - Current / Target amount
  - Estimasi tercapai
  - Tombol: Tambah Dana / Detail
- Tombol: + Buat Target Baru

**recurring/index.blade.php:**
- List upcoming (next 7 hari) di bagian atas
- Tabel semua recurring:
  - Nama, tipe, jumlah, frekuensi, next_run, status
  - Toggle aktif/nonaktif (Alpine.js)
  - Tombol edit/hapus

**household/index.blade.php:**
- Info household: nama, kode invite, plan
- Tombol: Salin Kode Invite / Generate Baru
- Tabel anggota:
  - Avatar, nama, email, role badge, bergabung sejak, aksi
  - Ubah role (superadmin only)
  - Keluarkan anggota (superadmin only)

**settings/index.blade.php:**
Tabs: Profil | Password | Household | Notifikasi | API Gemini

**superadmin/dashboard.blade.php:**
- Stat cards: total households, total users, transaksi hari ini
- Gemini API: usage hari ini / limit
- Storage: used / total
- Tabel 10 household terbaru
- Tabel 10 error terbaru dari log
- System health indicators

---

## KONFIGURASI

### .env (template)
```env
APP_NAME="FinanKu"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finanku
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="noreply@finanku.id"
MAIL_FROM_NAME="FinanKu"

GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
GEMINI_API_URL=https://generativelanguage.googleapis.com/v1beta/models

CRON_SECRET_KEY=
SUPERADMIN_EMAIL=
```

### config/gemini.php
```php
return [
    'api_key'   => env('GEMINI_API_KEY'),
    'model'     => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    'api_url'   => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models'),
    'timeout'   => 30,
    'retry'     => 2,
    'cache_ttl' => 86400, // 24 jam
];
```

### .htaccess (untuk deploy di shared hosting)
```apache
Options -MultiViews -Indexes
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

---

## COMPOSER PACKAGES

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "laravel/breeze": "^2.0",
        "laravel/sanctum": "^4.0",
        "maatwebsite/excel": "^3.1",
        "barryvdh/laravel-dompdf": "^2.0",
        "intervention/image": "^3.0",
        "guzzlehttp/guzzle": "^7.0"
    }
}
```

Install command:
```bash
composer require laravel/breeze maatwebsite/excel barryvdh/laravel-dompdf intervention/image
php artisan breeze:install blade
```

---

## PANDUAN DEPLOY MANUAL KE RUMAHWEB SMALL

Buat file docs/DEPLOYMENT.md dengan langkah-langkah berikut:

### Di Lokal (sebelum upload):
1. Pastikan semua fitur sudah dites
2. `composer install --optimize-autoloader --no-dev`
3. Update .env: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain.com`
4. `php artisan config:cache`
5. `php artisan route:cache`
6. `php artisan view:cache`
7. `php artisan storage:link` (lakukan di lokal, upload symlink atau gunakan URL langsung)
8. Export schema SQL: jalankan `php artisan migrate --pretend` dan simpan outputnya
9. Zip seluruh project TERMASUK folder vendor dan .env

### Di cPanel RumahWeb:
10. File Manager → public_html → Upload ZIP → Extract
11. Pindahkan semua isi folder `/public` ke `/public_html`
12. Edit `index.php` di public_html: update path ke `../` sesuai struktur
13. MySQL Database Wizard → buat database + user → assign all privileges
14. Update .env dengan kredensial database cPanel (format: usercpanel_namadb)
15. phpMyAdmin → import schema.sql
16. phpMyAdmin → jalankan seeder SQL secara manual

### Setup Cron di cron-job.org:
17. Daftar gratis di https://cron-job.org
18. Tambah job 1: `GET https://domain.com/cron/recurring` setiap hari jam 00:01
    Header: `X-Cron-Secret: [nilai CRON_SECRET_KEY]`
19. Tambah job 2: `GET https://domain.com/cron/reminder` setiap hari jam 08:00
    Header: `X-Cron-Secret: [nilai CRON_SECRET_KEY]`

### Verifikasi:
20. Buka https://domain.com → harus redirect ke /login
21. Register akun pertama
22. Masuk onboarding
23. Test upload struk (pastikan Gemini API key sudah diset di Settings)
24. Test semua fitur utama
25. Cek https://domain.com/superadmin/health

---

## SAAS LAYER — PLANNING (Belum Diimplementasi di UI)

Struktur database dan service sudah ada. Implementasi UI dan logika aktif dilakukan di fase berikutnya.

### Fase 2 — Beta Terbatas:
- Aktifkan landing page (/landing)
- Aktifkan enforcement plan limit di PlanLimitService
- Email transactional (welcome, invite, reset password)
- Superadmin bisa upgrade/downgrade plan household secara manual

### Fase 3 — SaaS Launch:
- Integrasi Midtrans untuk pembayaran subscription
- Halaman /pricing publik
- Referral system aktif
- Invoice PDF otomatis via email
- Upgrade hosting ke VPS/Cloud

### Fase 4 — Growth:
- Mobile app (Flutter atau React Native)
- API publik dengan dokumentasi
- Integrasi open banking Indonesia
- Laporan pajak otomatis (SPT)
- Enterprise plan dengan custom branding

---

## URUTAN PENGERJAAN (WAJIB DIIKUTI)

Ikuti urutan ini dengan ketat. Setelah setiap tahap, update PROGRESS.md.

```
TAHAP 1 — FOUNDATION
[ ] 1.1 Install Laravel + packages (composer)
[ ] 1.2 Semua Migration (001 sampai 023, urut)
[ ] 1.3 Semua Seeder
[ ] 1.4 Semua Traits (BelongsToHousehold, HasAuditLog, HasSoftDelete)
[ ] 1.5 File konfigurasi (config/gemini.php, .env template)
[ ] 1.6 php artisan migrate --seed (verifikasi)

TAHAP 2 — MODELS
[ ] 2.1 Model: User, Household, HouseholdMember, Plan, Subscription
[ ] 2.2 Model: Toko, TokoPola, Rekening, Kategori
[ ] 2.3 Model: Transaksi, TransaksiItem
[ ] 2.4 Model: Budget, SavingsGoal, RecurringTransaction
[ ] 2.5 Model: Tag, ActivityLog, Notification, Setting
[ ] 2.6 Model: Payment, Referral (SaaS — struktur saja)

TAHAP 3 — EXCEPTIONS & SERVICES CORE
[ ] 3.1 Exceptions: GeminiException, GeminiLimitException
[ ] 3.2 GeminiService.php
[ ] 3.3 DedupService.php
[ ] 3.4 OCRService.php + ImageService.php
[ ] 3.5 TokoPolaService.php

TAHAP 4 — SERVICES PARSER
[ ] 4.1 BankMutasiParser/BankParserInterface.php
[ ] 4.2 BankMutasiParser/BCAParser.php + MandiriParser.php
[ ] 4.3 BankMutasiParser/BNIParser.php + BSIParser.php + GenericParser.php
[ ] 4.4 MarketplaceParser/ShopeeParser.php + TiktokShopParser.php
[ ] 4.5 BankMutasiImportService.php

TAHAP 5 — SERVICES FINANCE
[ ] 5.1 LaporanService.php
[ ] 5.2 BudgetService.php
[ ] 5.3 InsightService.php + AnomalyDetectionService.php
[ ] 5.4 ExportService.php
[ ] 5.5 NotificationService.php
[ ] 5.6 PlanLimitService.php + ReferralService.php (placeholder)

TAHAP 6 — MIDDLEWARE & FORM REQUESTS
[ ] 6.1 Semua Middleware
[ ] 6.2 Semua Form Requests
[ ] 6.3 Register middleware di bootstrap/app.php

TAHAP 7 — CONTROLLERS
[ ] 7.1 Auth Controllers (Register, Login, Password)
[ ] 7.2 OnboardingController
[ ] 7.3 DashboardController
[ ] 7.4 TransaksiController (semua methods)
[ ] 7.5 ImportController
[ ] 7.6 LaporanController
[ ] 7.7 BudgetController + SavingsGoalController
[ ] 7.8 RecurringController + CronController
[ ] 7.9 HouseholdController + RekenungController
[ ] 7.10 KategoriController + TagController
[ ] 7.11 SettingsController + NotificationController
[ ] 7.12 SuperadminController
[ ] 7.13 Api/V1 Controllers

TAHAP 8 — ROUTES
[ ] 8.1 routes/web.php (semua route)
[ ] 8.2 routes/api.php
[ ] 8.3 php artisan route:list (verifikasi)

TAHAP 9 — VIEWS
[ ] 9.1 layouts/app.blade.php + auth.blade.php + superadmin.blade.php
[ ] 9.2 Semua components (8 komponen)
[ ] 9.3 Auth views (login, register, onboarding 5 steps)
[ ] 9.4 dashboard.blade.php
[ ] 9.5 transaksi/ (index, create, show, edit)
[ ] 9.6 import/preview.blade.php
[ ] 9.7 laporan/ (index, harian, mingguan, bulanan)
[ ] 9.8 budget/index.blade.php
[ ] 9.9 savings-goals/ (index, create, show)
[ ] 9.10 recurring/index.blade.php
[ ] 9.11 rekening/index.blade.php
[ ] 9.12 household/ (index, members)
[ ] 9.13 settings/index.blade.php
[ ] 9.14 notifications/index.blade.php
[ ] 9.15 superadmin/ (dashboard, households, users, logs, health)

TAHAP 10 — FINISHING
[ ] 10.1 .htaccess untuk shared hosting
[ ] 10.2 docs/DEPLOYMENT.md
[ ] 10.3 docs/SAAS_ROADMAP.md
[ ] 10.4 docs/DATABASE.md (ERD tekstual)
[ ] 10.5 docs/API.md
[ ] 10.6 docs/CHANGELOG.md
[ ] 10.7 Test end-to-end semua fitur utama
[ ] 10.8 php artisan optimize (untuk production)
```

---

## CATATAN PENTING (BACA SEBELUM MULAI)

1. **Angka uang:** SELALU gunakan `DECIMAL(15,2)` — JANGAN pernah `float`
2. **Format Rupiah:** "Rp 1.250.000" (titik pemisah ribuan, tanpa desimal)
3. **Format tanggal UI:** "10 Mei 2026" (Bahasa Indonesia)
4. **Format tanggal DB:** `YYYY-MM-DD`
5. **Bahasa UI:** Semua teks dalam Bahasa Indonesia
6. **Error message:** Bahasa Indonesia yang ramah dan informatif
7. **Validasi upload:** Max 5MB, cek mime type ketat (bukan hanya extension)
8. **Gemini API:** Selalu `try-catch`, selalu ada fallback ke form manual
9. **Dedup check:** Jalankan SEBELUM simpan ke DB, bukan sesudah
10. **Soft delete:** SEMUA tabel keuangan — JANGAN hard delete data keuangan
11. **Global scope:** SEMUA query data keuangan WAJIB ada filter `household_id`
12. **Permission di View:** Sembunyikan tombol yang tidak boleh diakses (bukan hanya route protection)
13. **Cache driver:** Gunakan `database` — JANGAN `file` atau `redis` (shared hosting)
14. **SaaS layer:** Struktur DB ada, Service ada tapi bypass, UI JANGAN ditampilkan dulu
15. **PROGRESS.md:** Update WAJIB setiap kali 1 file atau 1 grup selesai dibuat
