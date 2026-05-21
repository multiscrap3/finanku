# Checklist Kepatuhan UU PDP (UU No. 27 Tahun 2022)
## Aplikasi: Finanku — Manajemen Keuangan Rumah Tangga

> Undang-Undang Perlindungan Data Pribadi (UU PDP) disahkan pada 17 Oktober 2022.
> Masa transisi kepatuhan penuh: **Oktober 2024**.
> Dokumen ini mencatat status implementasi dan referensi file terkait.

---

## A. Landasan Hukum & Kebijakan

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| A1 | Halaman **Kebijakan Privasi** yang dapat diakses publik | ✅ Selesai | `resources/views/privacy/policy.blade.php` |
| A2 | Halaman **Syarat & Ketentuan** yang dapat diakses publik | ✅ Selesai | `resources/views/privacy/terms.blade.php` |
| A3 | Versi kebijakan privasi tercatat (versioning) | ✅ Selesai | Kolom `privacy_policy_version` di tabel `users` |
| A4 | Tanggal efektif kebijakan tercantum | ✅ Selesai | Tertera di halaman kebijakan privasi |
| A5 | Identitas & kontak Pengendali Data (DPO) | ✅ Selesai | Tertera di halaman kebijakan privasi |
| A6 | Tujuan pengolahan data tercantum jelas | ✅ Selesai | Tertera di halaman kebijakan privasi |
| A7 | Dasar hukum pengolahan data disebutkan | ✅ Selesai | Tertera di halaman kebijakan privasi |

---

## B. Persetujuan (Consent)

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| B1 | **Persetujuan eksplisit** saat registrasi (checkbox wajib) | ✅ Selesai | `resources/views/auth/register.blade.php` |
| B2 | Link ke Kebijakan Privasi & Syarat di form registrasi | ✅ Selesai | `resources/views/auth/register.blade.php` |
| B3 | **Log persetujuan** tersimpan (waktu, IP, versi kebijakan) | ✅ Selesai | Tabel `consent_logs` + `app/Models/ConsentLog.php` |
| B4 | Kolom `consent_given_at` di tabel users | ✅ Selesai | Migration `2024_01_02_000002_add_pdp_consent_to_users` |
| B5 | Penolakan consent = tidak bisa registrasi | ✅ Selesai | Validasi di `RegisterController` |
| B6 | Hak **menarik persetujuan** (withdraw consent / hapus akun) | ✅ Selesai | `resources/views/privacy/data-export.blade.php` |

---

## C. Hak Subjek Data

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| C1 | **Hak Akses** — pengguna dapat melihat data pribadinya | ✅ Selesai | `resources/views/privacy/data-export.blade.php` |
| C2 | **Hak Portabilitas** — unduh semua data pribadi (JSON) | ✅ Selesai | `PrivacyController@downloadData` |
| C3 | **Hak Koreksi** — ubah nama, email, telepon | ✅ Ada | `SettingController@updateProfile` |
| C4 | **Hak Hapus** — hapus akun & seluruh data | ✅ Ada | `ProfileController@destroy` |
| C5 | **Hak Pembatasan** — nonaktifkan notifikasi/pemasaran | ✅ Ada | `SettingController@updatePreferences` |
| C6 | **Hak Objeksi** terhadap pemrosesan data untuk AI/OCR | ✅ Selesai | Toggle opt-out di Settings > Preferensi |
| C7 | Waktu respons permintaan hak subjek ≤ 14 hari | ✅ Selesai | Dicantumkan di Kebijakan Privasi |

---

## D. Keamanan Data

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| D1 | Password di-hash (bcrypt, rounds=12) | ✅ Ada | `.env.example` `BCRYPT_ROUNDS=12` |
| D2 | Session berbasis database (bukan cookie plaintext) | ✅ Ada | `config/session.php` |
| D3 | CSRF protection pada semua form | ✅ Ada | Laravel middleware `VerifyCsrfToken` |
| D4 | Soft delete untuk data transaksi (tidak langsung hilang) | ✅ Ada | Trait `HasSoftDelete` di model |
| D5 | Audit log perubahan data (old_values, new_values, IP) | ✅ Ada | Tabel `audit_log`, Trait `HasAuditLog` |
| D6 | Field sensitif disanitasi di activity log | ✅ Ada | `LogActivityMiddleware` sanitize password |
| D7 | Enkripsi session diaktifkan di production | ✅ Selesai | `AppServiceProvider` — aktif otomatis saat `APP_ENV=production` |
| D8 | HTTPS enforced di production | ✅ Selesai | `AppServiceProvider` `URL::forceScheme('https')` |
| D9 | Rate limiting pada endpoint login & register | ✅ Selesai | `throttle:5,1` login, `throttle:3,1` register di `routes/web.php` |
| D10 | File upload divalidasi (tipe & ukuran) | ✅ Ada | `ProfileController`, `OCRController` |

---

## E. Pengolahan Data Pihak Ketiga

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| E1 | Pengungkapan penggunaan **Google Gemini API** (OCR/AI) | ✅ Selesai | Tertera di Kebijakan Privasi |
| E2 | Pengungkapan data yang dikirim ke Gemini API | ✅ Selesai | Tertera di Kebijakan Privasi |
| E3 | Dasar hukum transfer data ke pihak ketiga | ✅ Selesai | Tertera di Kebijakan Privasi |
| E4 | Data dari bank statement import tidak disimpan permanen | ✅ Selesai | `BankImportService` — auto-delete file setelah import |
| E5 | Kontrak pemrosesan data dengan Google (DPA) | ⚠️ Perlu tindakan | Daftarkan ke Google Workspace/Cloud DPA |

---

## F. Retensi & Penghapusan Data

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| F1 | Kebijakan retensi data tercantum (berapa lama data disimpan) | ✅ Selesai | Tertera di Kebijakan Privasi |
| F2 | Penghapusan data otomatis setelah akun dihapus | ✅ Selesai | `ProfileController@destroy` — hapus file OCR, bank, foto |
| F3 | File upload (foto profil, receipt OCR, bank) dihapus saat akun dihapus | ✅ Selesai | `ProfileController@destroy` — loop `OcrHistory` + `ImportBank` |
| F4 | Log audit dihapus setelah periode retensi | ✅ Selesai | `CronController@purgeAuditLog` — purge `audit_log` > 2 tahun |
| F5 | Session kedaluwarsa dibersihkan secara rutin | ✅ Ada | Laravel session garbage collection |

---

## G. Pelanggaran Data (Data Breach)

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| G1 | Prosedur notifikasi pelanggaran data dalam **14 hari** ke BSSN | ⚠️ Perlu SOP | Buat prosedur operasional tertulis |
| G2 | Notifikasi ke pengguna terdampak dalam **14 hari** | ✅ Selesai | `app/Mail/DataBreachNotification.php` + `resources/views/emails/data-breach-notification.blade.php` |
| G3 | Log insiden keamanan | ✅ Selesai | Tabel `security_logs` + `app/Models/SecurityLog.php` — log login gagal, export data, hapus akun |

---

## H. Transparansi Penggunaan AI

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| H1 | Informasikan penggunaan AI untuk analisis keuangan | ✅ Selesai | Tertera di Kebijakan Privasi |
| H2 | Pengguna dapat opt-out dari fitur AI | ✅ Selesai | Toggle `ai_opt_out` & `ai_ocr_opt_out` di Settings > Preferensi |
| H3 | Data OCR (gambar struk) tidak disimpan di server eksternal | ⚠️ Perlu konfirmasi | Review dokumentasi Gemini API / Google AI Studio |

---

## I. Antarmuka Pengguna (UI/UX Compliance)

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| I1 | Tab **Privasi & Data** di halaman Settings | ✅ Selesai | `resources/views/settings/index.blade.php` |
| I2 | Halaman **Ekspor Data Pribadi** (download all my data) | ✅ Selesai | `resources/views/privacy/data-export.blade.php` |
| I3 | Link Kebijakan Privasi di footer auth pages | ✅ Selesai | `resources/views/layouts/auth.blade.php` |
| I4 | Link Kebijakan Privasi di footer dashboard/app | ✅ Selesai | `resources/views/layouts/app.blade.php` |

---

## J. Import Bank (Tambahan)

| # | Kewajiban | Status | File/Lokasi |
|---|-----------|--------|-------------|
| J1 | Banner info PDP sebelum upload | ✅ Selesai | `resources/views/import-bank/form.blade.php` |
| J2 | File bank statement auto-delete setelah import | ✅ Selesai | `BankImportService` blok `finally` |
| J3 | Masking data sensitif di error log | ✅ Selesai | `BankImportService::maskErrors()` |
| J4 | Tombol hapus file bank secara manual | ✅ Selesai | `ImportBankController@deleteFile` |
| J5 | Info retensi file di halaman riwayat import | ✅ Selesai | `resources/views/import-bank/index.blade.php` |
| J6 | Cron purge file bank > 30 hari | ✅ Selesai | `CronController@purgeImportFiles` |

---

## Ringkasan Status

| Kategori | Total | Selesai | Perlu Tindakan |
|----------|-------|---------|----------------|
| A. Landasan Hukum | 7 | 7 | 0 |
| B. Persetujuan | 6 | 6 | 0 |
| C. Hak Subjek Data | 7 | 7 | 0 |
| D. Keamanan Data | 10 | 10 | 0 |
| E. Pihak Ketiga | 5 | 4 | 1 |
| F. Retensi Data | 5 | 5 | 0 |
| G. Data Breach | 3 | 2 | 1 |
| H. Transparansi AI | 3 | 2 | 1 |
| I. UI/UX | 4 | 4 | 0 |
| J. Import Bank | 6 | 6 | 0 |
| **TOTAL** | **56** | **53** | **3** |

---

## Sisa 3 Item (Tidak Bisa Dikerjakan di Kode)

| # | Item | Tindakan |
|---|------|---------|
| E5 | Daftar ke **Google Cloud Data Processing Agreement** | Masuk Google Cloud Console → Legal → Data Processing Amendment |
| G1 | SOP notifikasi pelanggaran data ke **BSSN** dalam 14 hari | Buat dokumen prosedur operasional internal |
| H3 | Konfirmasi data gambar OCR tidak di-persist oleh Gemini | Review [dokumentasi Gemini API](https://ai.google.dev/gemini-api/terms) — periksa data retention policy Google AI |

---

## Jadwal Cron yang Direkomendasikan

```bash
# Purge file bank statement > 30 hari (setiap hari jam 02:00)
0 2 * * * curl -s -X POST https://yourdomain.com/cron/purge-import-files \
  -H "X-Cron-Secret: YOUR_SECRET"

# Purge audit log > 2 tahun (setiap bulan tanggal 1 jam 03:00)
0 3 1 * * curl -s -X POST https://yourdomain.com/cron/purge-audit-log \
  -H "X-Cron-Secret: YOUR_SECRET"
```

---

*Dokumen ini dibuat sesuai UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi.*
*Terakhir diperbarui: 2026-05-21*
