# Changelog — FinanKu

Semua perubahan penting pada aplikasi FinanKu didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
dan project ini menggunakan [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- Middleware: HouseholdMiddleware, RoleMiddleware, LogActivityMiddleware, CheckPlanLimitMiddleware
- Controllers: KategoriController, SumberTransaksiController, HutangPiutangController, RecurringTransaksiController, TagController, HouseholdController, SettingController, NotifikasiController, AuthController, ProfileController
- Services: HutangPiutangService, NotifikasiService, ExportService, ImportService, RecurringService
- Form Requests: StoreTabunganRequest, StoreHutangPiutangRequest, UpdateProfileRequest, UpdatePasswordRequest, JoinHouseholdRequest, ImportFileRequest
- Views: Semua blade views (30+ views)
- Frontend: Dashboard, Transaksi, Laporan, Anggaran, Tabungan, Settings

---

## [1.0.0] — 2026-05-18

### Rilis Pertama — Backend Core (54%)

Versi awal FinanKu dengan pondasi backend yang solid: database layer lengkap,
model-model utama, core services, dan controller pertama.

### Added

#### Database Layer (100%)
- 23 migrations: plans, households, users, sumber_transaksi, kategori, transaksi, anggaran, tabungan, tabungan_transaksi, hutang_piutang, hutang_piutang_pembayaran, notifikasi, laporan, settings, audit_log, import_bank, ocr_history, ai_insights, backup_restore, household_invitations, payment_history, tags, transaksi_tags
- Foreign key constraints dan indexes pada semua tabel
- Soft delete pada tabel kritis (transaksi, tabungan, hutang_piutang)

#### Models (100%) — 19 Models
- `User` — dengan multi-household support dan plan subscription
- `Household` — manajemen keluarga/grup keuangan
- `Plan` — plan langganan (free/premium/family)
- `Kategori` — kategori transaksi (pemasukan/pengeluaran) per household
- `SumberTransaksi` — sumber dana (rekening, dompet, e-wallet) dengan saldo
- `Transaksi` — transaksi keuangan lengkap dengan bukti & tags
- `Anggaran` — budget per kategori per bulan
- `Tabungan` — goal-based savings dengan target & deadline
- `TabunganTransaksi` — riwayat setor/tarik tabungan
- `HutangPiutang` — manajemen hutang & piutang
- `HutangPiutangPembayaran` — riwayat pembayaran hutang/piutang
- `Notifikasi` — sistem notifikasi in-app
- `Laporan` — laporan keuangan tersimpan
- `Setting` — pengaturan per household
- `AuditLog` — audit trail aktivitas
- `ImportBank` — riwayat import mutasi bank
- `OcrHistory` — riwayat OCR struk belanja
- `AiInsights` — insight keuangan dari AI
- `Tag` — label/tag fleksibel untuk transaksi

#### Traits (100%) — 3 Traits
- `BelongsToHousehold` — scope otomatis filter by household
- `HasAuditLog` — auto-logging create/update/delete
- `HasSoftDelete` — soft delete helpers

#### Seeders (100%) — 5 Seeders
- `PlanSeeder` — data plan: Free, Premium, Family
- `SumberTransaksiSeeder` — data sumber transaksi default
- `SettingSeeder` — pengaturan default per household
- `KategoriSeeder` — (via DatabaseSeeder)
- `DatabaseSeeder` — orchestrator semua seeder

#### Services (50%) — 5/10 Services
- `TransaksiService` — CRUD transaksi, upload bukti, transfer antar sumber, auto-update saldo & anggaran
- `DashboardService` — agregasi data dashboard: total saldo, chart 6 bulan, top kategori, distribusi saldo
- `LaporanService` — generate laporan harian/mingguan/bulanan/tahunan, perbandingan periode, grouping by kategori
- `AnggaranService` — create/update/delete anggaran, monitoring realisasi vs target, auto-alert 80% & 100%, copy dari bulan sebelumnya
- `TabunganService` — create/update tabungan, setor/tarik dana, progress tracking, estimasi waktu tercapai, auto-notification saat target tercapai

#### Controllers (33%) — 5/15 Controllers
- `TransaksiController` — CRUD transaksi, upload struk, filter & search, soft delete & restore
- `DashboardController` — render dashboard dengan data agregasi, AJAX chart data
- `LaporanController` — generate & tampilkan laporan, export (PDF/Excel coming soon)
- `AnggaranController` — CRUD anggaran, summary AJAX, copy dari bulan lalu
- `TabunganController` — CRUD tabungan, setor & tarik dana

#### Form Requests (40%) — 4/10 Requests
- `StoreTransaksiRequest` — validasi create transaksi
- `UpdateTransaksiRequest` — validasi update transaksi
- `StoreAnggaranRequest` — validasi create anggaran (unique per kategori/bulan/tahun)
- `UpdateAnggaranRequest` — validasi update anggaran

#### Routes (100%)
- `web.php` — 50+ routes: auth, dashboard, transaksi, laporan, anggaran, tabungan, hutang-piutang, kategori, sumber, household, notifikasi, settings

#### Konfigurasi
- `config/app.php` — APP_VERSION ditambahkan
- `CONTEXT.md` — standar coding & konvensi proyek
- `VERSIONING.md` — panduan versioning

### Technical Stack
- **Framework:** Laravel 13 (PHP 8.3+)
- **Database:** MySQL
- **Frontend Build:** Vite 8 + Tailwind CSS 4
- **Authentication:** Laravel built-in (akan menggunakan Breeze)
- **Queue:** Database driver

---

## Panduan Format Entry

Setiap versi baru menggunakan format:

```
## [X.Y.Z] — YYYY-MM-DD

### Added       — fitur baru
### Changed     — perubahan fitur yang sudah ada
### Deprecated  — fitur yang akan dihapus
### Removed     — fitur yang dihapus
### Fixed       — bug fix
### Security    — perbaikan keamanan
```

[Unreleased]: https://github.com/username/finanku/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/username/finanku/releases/tag/v1.0.0
