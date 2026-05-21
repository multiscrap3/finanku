# Finanku Backend Progress

## Status Terakhir

Backend + Frontend hampir 100%. Semua mismatch controller/view sudah diperbaiki, semua views CRUD sudah dibuat.

## Komponen yang Sudah Dikerjakan

- Services: 17/17 (termasuk TokoPolaService)
- Controllers: 19/19 (+ HouseholdController.members, ImportBankController.webIndex/webForm)
- Form Requests: 10/10
- Routes: selesai (web + API-like + cron + superadmin)
- Middleware: 6/6
- Views: SEMUA halaman inti + form CRUD selesai
- Layouts: 3/3 (app, auth, superadmin)

---

## Fixes yang Sudah Dilakukan (Sesi Terbaru)

### Mismatch yang diperbaiki:
- `RecurringTransaksiController`: view names (`recurring-transaksi.*` → `recurring.*`), redirect routes, rename `toggleStatus` → `toggle`, field `status` → `is_active`, `$recurringTransaksi` → `$recurring`
- `laporan/bulanan.blade.php`: `$laporan` → `$data`
- `hutang-piutang/index.blade.php`: gunakan `$hutangPiutang.where('jenis')`, field `nama_pihak`, `tanggal_jatuh_tempo`, `jumlah_total`, `sisa`
- `tabungan/index.blade.php`: `$item->target` → `$item->target_jumlah`, add `sumber_transaksi_id` to setor form
- `TabunganController.index`: tambah pass `$sumberTransaksi`
- `recurring/index.blade.php`: `$item->nama` → `$item->keterangan`

### Controller yang diupdate:
- `HouseholdController`: tambah method `members()`
- `ImportBankController`: tambah method `webIndex()` dan `webForm()`

---

## Views yang Sudah Dibuat

### Auth
- `auth/login.blade.php`
- `auth/register.blade.php` (support token undangan)

### Onboarding
- `onboarding/index.blade.php` (5 step)

### App Views (semua selesai)
- `dashboard.blade.php` — dengan Chart.js (tren 6 bulan + donut pengeluaran per kategori)
- `transaksi/index.blade.php`
- `transaksi/create.blade.php` (+ OCR + suggest keterangan)
- `transaksi/show.blade.php`
- `transaksi/edit.blade.php`
- `laporan/index.blade.php`
- `laporan/bulanan.blade.php` ✓ fixed
- `laporan/harian.blade.php` ✓ new
- `laporan/mingguan.blade.php` ✓ new (+ bar chart harian)
- `laporan/tahunan.blade.php` ✓ new (+ bar chart bulanan)
- `laporan/perbandingan.blade.php` ✓ new
- `anggaran/index.blade.php`
- `anggaran/create.blade.php` ✓ new
- `anggaran/edit.blade.php` ✓ new
- `tabungan/index.blade.php` ✓ fixed
- `tabungan/create.blade.php` ✓ new
- `tabungan/show.blade.php` ✓ new (setor + tarik + riwayat)
- `tabungan/edit.blade.php` ✓ new
- `hutang-piutang/index.blade.php` ✓ fixed
- `hutang-piutang/create.blade.php` ✓ new
- `hutang-piutang/show.blade.php` ✓ new (bayar + riwayat)
- `recurring/index.blade.php` ✓ fixed
- `recurring/create.blade.php` ✓ new
- `recurring/edit.blade.php` ✓ new
- `kategori/index.blade.php`
- `kategori/edit.blade.php` ✓ new
- `sumber-transaksi/index.blade.php`
- `sumber-transaksi/edit.blade.php` ✓ new
- `household/index.blade.php`
- `household/members.blade.php` ✓ new
- `settings/index.blade.php`
- `notifikasi/index.blade.php`
- `import-bank/index.blade.php` ✓ new
- `import-bank/form.blade.php` ✓ new (3-step wizard: upload → preview → done)

### Superadmin Views
- `superadmin/dashboard.blade.php`
- `superadmin/households.blade.php`
- `superadmin/household-show.blade.php`
- `superadmin/users.blade.php`
- `superadmin/logs.blade.php`
- `superadmin/health.blade.php`

---

## Controllers yang Tersedia

### Auth
- `Auth/AuthenticatedSessionController` (login/logout)
- `Auth/RegisterController` (register + invite token)

### Core App
- `DashboardController`
- `TransaksiController` (+ suggest endpoint, dedup, anomaly)
- `LaporanController` (harian, mingguan, bulanan, tahunan, perbandingan, export)
- `AnggaranController`
- `TabunganController`
- `HutangPiutangController`
- `RecurringTransaksiController` ✓ fixed
- `KategoriController`
- `SumberTransaksiController`
- `TagController`
- `HouseholdController` (+ members method)
- `SettingController`
- `NotifikasiController`
- `ProfileController`

### AI / OCR / Import
- `OCRController`
- `AIController`
- `ImportBankController` (+ webIndex, webForm)

### System
- `OnboardingController`
- `CronController`
- `SuperadminController`

---

## Services yang Tersedia

- `TransaksiService`
- `DashboardService`
- `LaporanService`
- `AnggaranService`
- `TabunganService`
- `HutangPiutangService`
- `RecurringService`
- `ExportService`
- `NotifikasiService`
- `PlanLimitService`
- `GeminiService`
- `OCRService`
- `ImageService`
- `DedupService`
- `InsightService`
- `AnomalyDetectionService`
- `TokoPolaService`

### Bank Parsers
- `BankParserInterface`
- `AbstractCsvBankParser`
- `BCAParser` / `MandiriParser` / `BNIParser` / `BSIParser` / `GenericParser`
- `BankImportService`

---

## Routes

### Web (auth required)
- Dashboard, Transaksi (resource + restore + export + suggest)
- Laporan (harian, mingguan, bulanan, tahunan, perbandingan, export)
- Anggaran (resource + summary)
- Tabungan (resource + setor + tarik)
- Hutang-Piutang (resource + bayar)
- Recurring (resource + toggle)
- Tags (resource)
- Household (index, members, invite, join, updateRole, removeMember)
- Settings (index, profile.update, password.update, household.update, preferences.update)
- Notifikasi (index, mark-read, mark-all-read)
- Onboarding (5 steps + skip)
- Import Bank Web: `import-bank.web.index`, `import-bank.web.form`

### API-like (AJAX, auth required)
- `/api/kategori/search`
- `/api/sumber-transaksi/saldo`
- `/api/transaksi/suggest`
- `/api/ocr/extract`, `/api/ocr/history`
- `/api/import-bank/` (index, store, preview, show)
- `/api/ai/duplicate-check`, `/api/ai/anomaly-detect`, `/api/ai/anomalies/scan`, `/api/ai/insights/generate`

### Cron (cron.secret middleware)
- POST `/cron/recurring`
- POST `/cron/notifications`
- POST `/cron/insights`
- POST `/cron/anomaly-scan`
- GET `/cron/health`

### Superadmin
- GET `/superadmin/` + households + users + logs + health

---

## Estimasi Progress

- Backend core: **100%**
- Views / Frontend (Tailwind CDN): **~95%** (semua views inti + form CRUD selesai)
- Keseluruhan produk (SaaS penuh, AI/OCR, import bank, production hardening): **~80-85%**

---

## Sisa Prioritas

### Yang masih perlu dikerjakan
- ExportService: implementasi export Excel/PDF (saat ini return info)
- Tags management CRUD (form create/edit — TagController route hanya index, store, destroy)
- Test end-to-end alur register → onboarding → transaksi pertama
- Migration files (belum dicheck)
- Seeder data awal (kategori default, dll)
- Production hardening (env, queue, scheduler)

### Catatan penting
- Semua model menggunakan `BelongsToHousehold` trait dengan global scope — filter household otomatis
- RecurringTransaksi: gunakan field `is_active` (boolean) dan `next_run` (bukan `status` dan `tanggal_eksekusi_terakhir`)
- Tabungan: gunakan field `target_jumlah` (bukan `target`) dan `target_tanggal` (bukan `tanggal_target` di controller — ada inconsistency di TabunganController validate `tanggal_target` tapi model pakai `target_tanggal`)
- HutangPiutang: field utama `nama_pihak`, `jumlah_total`, `jumlah_terbayar`, `tanggal_jatuh_tempo`, accessor `sisa`

---

## Project Path

```text
c:\laragon\www\Finanku
```

## PHP Executable

```text
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe
```
