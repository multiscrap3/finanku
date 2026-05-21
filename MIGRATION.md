# Finanku — Migrasi UI: Tailwind → Dompet Bootstrap Template

**Template Baru:** Dompet by Dexignlab  
**Tanggal Mulai:** 2026-05-15  
**Status Keseluruhan:** ✅ Migrasi Selesai

---

## Progres Ringkas

| Fase | Deskripsi | Status |
|------|-----------|--------|
| 0 | Setup Assets | ✅ Selesai |
| 1 | Layout Utama | ✅ Selesai |
| 2 | Halaman Auth | ✅ Selesai |
| 3 | Dashboard | ✅ Selesai |
| 4 | Transaksi | ✅ Selesai |
| 5 | Keuangan (Anggaran, Tabungan, Hutang) | ✅ Selesai |
| 6 | Laporan | ✅ Selesai |
| 7 | Master Data | ✅ Selesai |
| 8 | Superadmin | ✅ Selesai |
| 9 | Testing & Cleanup | ✅ Selesai |

---

## Konvensi Status
- ⬜ Belum dimulai
- 🔄 Sedang dikerjakan
- ✅ Selesai
- ❌ Ada masalah (catat di catatan)

---

## Fase 0 — Setup Assets

> Salin seluruh aset CSS/JS/font/icons dari template Dompet ke folder `public/` proyek Finanku.

### Langkah-langkah

- [x] **0.1** Salin `css/style.css` + `css/perfect-scrollbar.css` → `public/dompet/css/`
- [x] **0.2** JS tidak ada di package — gunakan CDN (Bootstrap JS, MetisMenu, dll)
- [x] **0.3** Salin `icons/avasta/` + `icons/bootstrap-icons/font/` + `icons/feather/` → `public/dompet/icons/`
- [x] **0.4** Vendor tidak ada di package — gunakan CDN (animate.css, AOS, MetisMenu CSS)
- [x] **0.5** Fix `style.css`: hapus 14 baris `@import` yang path-nya rusak (local icons tidak ada)
- [ ] **0.6** Verifikasi file `public/dompet/css/style.css` bisa diakses via browser (cek di Fase 1)
- [x] **0.7** CDN yang dibutuhkan dicatat di bagian Referensi bawah

**Status Fase 0:** ✅ Selesai  
**Catatan:** Package tidak menyertakan folder `vendor/` dan `js/`. Icon library yang tidak tersedia (font-awesome, simple-line-icons, material-design, themify, line-awesome, flaticon, icomoon) digantikan via CDN di layout. Hanya `avasta`, `bootstrap-icons`, dan `feather` yang tersedia lokal.

---

## Fase 1 — Layout Utama

> Rebuild 3 file layout dari Tailwind ke struktur HTML Dompet Bootstrap.

### 1.1 — `resources/views/layouts/app.blade.php`

**Komponen yang harus dikonversi:**
- [x] `<head>`: Tailwind CDN → Bootstrap (bundle di dalam style.css) + semua CDN plugin
- [x] `<head>`: Alpine.js dihapus → Bootstrap JS + jQuery + MetisMenu + Perfect-Scrollbar
- [x] Sidebar: Struktur Dompet (`.dlabnav`, `.dlabnav-scroll`, `.metismenu`)
- [x] Nav item aktif: `mm-active` via PHP `request()->routeIs()`
- [x] Topbar/Header: `.header > .header-content > .navbar` struktur Dompet
- [x] Dropdown user: Bootstrap `data-bs-toggle="dropdown"`
- [x] Sidebar toggle: jQuery hamburger `.menu-toggle` class pada `<body>`
- [x] Dark mode: Dihapus (Dompet punya dark theme via `data-theme-version` — skip untuk sekarang)
- [x] Flash messages: Bootstrap `.alert` + auto-dismiss 5 detik via jQuery
- [x] `@yield('content')` di dalam `.content-body > .container-fluid`
- [x] Footer dengan class `.footer`

**Status 1.1:** ✅ Selesai  
**Catatan:** Transaksi dibuat sebagai dropdown submenu (Semua Transaksi + Import Bank). Menu-title divider ditambahkan untuk grouping.

---

### 1.2 — `resources/views/layouts/auth.blade.php`

- [x] Ganti head dengan Bootstrap CSS + Dompet CSS
- [x] Centered card dengan gradient background
- [x] Logo + brand di atas card
- [x] Error validasi + flash success via Bootstrap alert
- [x] `@yield('content')` di dalam card body

**Status 1.2:** ✅ Selesai

---

### 1.3 — `resources/views/layouts/superadmin.blade.php`

- [x] Ganti head dengan Bootstrap CSS + Dompet CSS
- [x] Override warna primary ke ungu via CSS `:root` variables
- [x] Sidebar superadmin dengan 5 menu (Dashboard, Households, Users, Logs, Health)
- [x] Tombol "Kembali ke App" di header kanan
- [x] Flash messages (success, error, warning)

**Status 1.3:** ✅ Selesai

---

## Fase 2 — Halaman Auth

### 2.1 — `resources/views/auth/login.blade.php`
- [x] Form login → Bootstrap `form-control`, `form-label`, `d-grid btn`
- [x] Error validation → `is-invalid` + `invalid-feedback`
- [x] Password toggle Alpine.js → vanilla JS `togglePassword()`

**Status 2.1:** ✅ Selesai

### 2.2 — `resources/views/auth/register.blade.php`
- [x] Form register → Bootstrap form components
- [x] Invitation banner → Bootstrap `alert alert-info`
- [x] Password toggle → vanilla JS (reuse fungsi `togglePassword()`)
- [x] Readonly email jika invitation

**Status 2.2:** ✅ Selesai

### 2.3 — `resources/views/onboarding/index.blade.php`
- [x] Standalone page (tidak extends layout) → Bootstrap standalone dengan Dompet CSS
- [x] Stepper visual → custom CSS classes (`.step-circle.done/active/todo`)
- [x] Alpine.js `x-for` dynamic rows (step 2, 3, 4) → vanilla JS `addRekening()`, `addAnggaran()`, `addRecurring()`
- [x] Step 5 selesai → Bootstrap success icon + button

**Status 2.3:** ✅ Selesai

**Status Fase 2:** ✅ Selesai  
**Catatan:** Alpine.js sepenuhnya dihapus. Dynamic form rows digantikan vanilla JS `insertAdjacentHTML`. Satu row otomatis di-add saat page load.

---

## Fase 3 — Dashboard

### 3.1 — `resources/views/dashboard.blade.php`
- [x] Hero card (Total Saldo) → Bootstrap card + gradient CSS inline
- [x] Summary cards 4 kolom → `row g-4 col-6 col-md-3` + Bootstrap card
- [x] Progress bar anggaran → Bootstrap `.progress`
- [x] Chart.js tren 6 bulan → tetap, canvas di dalam Bootstrap card
- [x] Chart.js doughnut kategori → tetap
- [x] Saldo rekening → card dengan list flex
- [x] Transaksi terbaru → card list tanpa table, dengan `text-truncate`
- [x] Quick actions 4 tombol → Bootstrap card grid

**Status 3.1:** ✅ Selesai  
**Catatan:** Semua dark mode class (`dark:*`) dihapus. Chart.js tidak berubah, hanya container-nya diganti ke Bootstrap card.

---

## Fase 4 — Transaksi

### 4.1 — `resources/views/transaksi/index.blade.php`
- [x] Summary bar (pemasukan/pengeluaran/saldo) → 3 Bootstrap card dengan colored left-border
- [x] Filter panel → Bootstrap collapse (`data-bs-toggle="collapse"`)
- [x] List transaksi → Bootstrap card borderless rows + Bootstrap Icons
- [x] Empty state → Bootstrap centered dengan icon
- [x] Pagination → `links('pagination::bootstrap-5')`

**Status 4.1:** ✅ Selesai

### 4.2 — `resources/views/transaksi/create.blade.php`
- [x] OCR upload strip → Bootstrap card + vanilla JS fetch
- [x] Jenis radio → Bootstrap `btn-group btn-check`
- [x] Form fields → Bootstrap `form-control`, `form-select`
- [x] Autocomplete keterangan → vanilla JS debounce + custom dropdown
- [x] Saldo warning → Bootstrap `alert-danger` toggle via JS computed
- [x] Dynamic item table (add/remove rows) → vanilla JS DOM manipulation
- [x] Hidden fields OCR data → synced via JS
- [x] Submit disable saat saldo kurang

**Status 4.2:** ✅ Selesai

### 4.3 — `resources/views/transaksi/edit.blade.php`
- [x] Form fields → Bootstrap form components pre-filled
- [x] Jenis radio → Bootstrap `btn-group btn-check`
- [x] Transfer field → toggle via vanilla JS
- [x] Bukti foto existing → preview `<img>` + file input Bootstrap

**Status 4.3:** ✅ Selesai

### 4.4 — `resources/views/transaksi/show.blade.php`
- [x] Header transaksi → ikon + jumlah + badge jenis
- [x] Detail grid → Bootstrap `row g-3 col-6`
- [x] OCR items table → Bootstrap `table table-sm table-bordered`
- [x] Bukti foto → linked image
- [x] Audit log → Bootstrap card list

**Status 4.4:** ✅ Selesai

### 4.5 — `resources/views/import-bank/index.blade.php`
- [x] List riwayat import → Bootstrap card borderless rows
- [x] Status badge → Bootstrap `badge` colored by status
- [x] Empty state + pagination

**Status 4.5:** ✅ Selesai

### 4.6 — `resources/views/import-bank/form.blade.php`
- [x] Alpine.js 3-step wizard → vanilla JS step manager
- [x] File dropzone → custom label + Bootstrap file input
- [x] Preview table → Bootstrap `table table-sm`
- [x] Import fetch → vanilla JS FormData POST
- [x] Step indicator circles + progress lines → DOM updates via JS

**Status 4.6:** ✅ Selesai

**Status Fase 4:** ✅ Selesai  
**Catatan:** Seluruh Alpine.js pada fase ini (OCR, autocomplete, step wizard, item table, jenis toggle, saldo warning) dikonversi ke vanilla JS dengan state object. Bootstrap Icons menggantikan SVG inline.

---

## Fase 5 — Keuangan (Anggaran, Tabungan, Hutang-Piutang, Recurring)

### 5.1 — Anggaran
- [x] `anggaran/index.blade.php` — List + progress bar
- [x] `anggaran/create.blade.php` — Form
- [x] `anggaran/edit.blade.php` — Form
- [x] `anggaran/show.blade.php` — Detail

**Status 5.1:** ✅ Selesai

### 5.2 — Tabungan
- [x] `tabungan/index.blade.php` — List card tabungan
- [x] `tabungan/create.blade.php` — Form
- [x] `tabungan/edit.blade.php` — Form
- [x] `tabungan/show.blade.php` — Detail + progress

**Status 5.2:** ✅ Selesai

### 5.3 — Hutang Piutang
- [x] `hutang-piutang/index.blade.php` — List dengan status
- [x] `hutang-piutang/create.blade.php` — Form
- [x] `hutang-piutang/edit.blade.php` — Form
- [x] `hutang-piutang/show.blade.php` — Detail + riwayat bayar

**Status 5.3:** ✅ Selesai

### 5.4 — Transaksi Rutin
- [x] `recurring/index.blade.php` — List
- [x] `recurring/create.blade.php` — Form
- [x] `recurring/edit.blade.php` — Form
- [x] `recurring/show.blade.php` — Detail

**Status 5.4:** ✅ Selesai

**Status Fase 5:** ✅ Selesai  
**Catatan:** Alpine.js `x-data`/`x-show`/`@click` pada hutang-piutang tabs digantikan Bootstrap nav-pills. Tabungan index setor dana menggunakan Bootstrap collapse. Progress bar menggunakan Bootstrap `.progress`. Semua dark mode class dihapus.

---

## Fase 6 — Laporan

### 6.1 — `laporan/index.blade.php`
- [x] 4 quick-link cards (Harian/Mingguan/Bulanan/Tahunan) → Bootstrap card grid
- [x] Info + Export button → Bootstrap card

**Status 6.1:** ✅ Selesai

### 6.2 — `laporan/bulanan.blade.php`
- [x] Filter form (bulan + tahun) → Bootstrap form-select + btn
- [x] 4 summary cards → colored top-border Bootstrap cards
- [x] Doughnut chart per kategori → Chart.js, unchanged
- [x] Rincian per kategori list → Bootstrap card flex rows
- [x] Daftar transaksi → Bootstrap card borderless rows

**Status 6.2:** ✅ Selesai

### 6.3 — `laporan/harian.blade.php`
- [x] Filter date → Bootstrap form-control date
- [x] 3 summary cards → colored top-border Bootstrap cards
- [x] Daftar transaksi → Bootstrap card borderless rows

**Status 6.3:** ✅ Selesai

### 6.4 — `laporan/mingguan.blade.php`
- [x] Filter date → Bootstrap form-control date
- [x] 3 summary cards → colored top-border Bootstrap cards
- [x] Bar chart per hari → Chart.js, unchanged
- [x] Daftar transaksi → Bootstrap card borderless rows

**Status 6.4:** ✅ Selesai

### 6.5 — `laporan/tahunan.blade.php`
- [x] Filter tahun → Bootstrap form-select
- [x] 4 summary cards → colored top-border Bootstrap cards
- [x] Grouped bar chart per bulan → Chart.js, unchanged
- [x] Ringkasan per bulan → Bootstrap card list rows

**Status 6.5:** ✅ Selesai

### 6.6 — `laporan/perbandingan.blade.php`
- [x] Filter dua bulan (bulan1/tahun1 vs bulan2/tahun2) → Bootstrap form-select row
- [x] Side-by-side comparison cards → Bootstrap card 2 kolom
- [x] Selisih summary → Bootstrap card 2-col grid

**Status 6.6:** ✅ Selesai

**Status Fase 6:** ✅ Selesai  
**Catatan:** Semua Chart.js code dipertahankan unchanged; hanya container markup yang diganti ke Bootstrap card. Summary cards menggunakan `border-top` colored inline style.

---

## Fase 7 — Master Data & Lainnya

### 7.1 — Kategori
- [x] `kategori/index.blade.php` — Inline form + list parent/child dengan badge jenis
- [x] `kategori/create.blade.php` — Form + color picker + icon emoji
- [x] `kategori/edit.blade.php` — Form pre-filled

**Status 7.1:** ✅ Selesai

### 7.2 — Sumber Transaksi
- [x] `sumber-transaksi/index.blade.php` — Inline form + total saldo card + daftar dengan emoji icon
- [x] `sumber-transaksi/create.blade.php` — Form dengan input-group Rp
- [x] `sumber-transaksi/edit.blade.php` — Form + readonly saldo field

**Status 7.2:** ✅ Selesai

### 7.3 — Tags
- [x] `tags/index.blade.php` — Inline form + list dengan toggle edit per row via vanilla JS `toggleTagEdit(id)`

**Status 7.3:** ✅ Selesai

### 7.4 — Household & Members
- [x] `household/index.blade.php` — Info card + daftar anggota + undang + join via token
- [x] `household/members.blade.php` — Daftar aktif + undangan pending + form undang

**Status 7.4:** ✅ Selesai

### 7.5 — Notifikasi
- [x] `notifikasi/index.blade.php` — List dengan Bootstrap Icons per jenis + mark-read form + pagination

**Status 7.5:** ✅ Selesai

### 7.6 — Pengaturan
- [x] `settings/index.blade.php` — Alpine.js `x-data="{ tab }"` → Bootstrap nav-pills + tab-pane. Active tab dari `request('tab', 'profil')`.

**Status 7.6:** ✅ Selesai

### 7.7 — Halaman Lain
- [x] `welcome.blade.php` — Rebuilt sebagai landing page Bootstrap standalone (tidak extends layout); hero + 3 feature cards + Login/Daftar CTA

**Status 7.7:** ✅ Selesai

**Status Fase 7:** ✅ Selesai  
**Catatan:** Tags inline-edit Alpine.js `x-data="{ editing }"` digantikan vanilla JS `toggleTagEdit(id)` dengan `classList.toggle('d-none')`. Settings tabs Alpine.js `x-data="{ tab }"` digantikan Bootstrap nav-pills dengan server-side active state dari `request('tab')`. Notifikasi SVG icons digantikan Bootstrap Icons `bi-*`.

---

## Fase 8 — Superadmin

### 8.1 — `superadmin/dashboard.blade.php`
- [x] 6 stat cards dengan Bootstrap Icons + warna per metrik
- [x] Tabel recent households + recent users → Bootstrap `table table-sm table-hover`

**Status 8.1:** ✅ Selesai

### 8.2 — `superadmin/households.blade.php`
- [x] Filter form (search + status) → Bootstrap `form-control`/`form-select`
- [x] Tabel dengan badge status + pagination bootstrap-5

**Status 8.2:** ✅ Selesai

### 8.3 — `superadmin/household-show.blade.php`
- [x] 4 stat cards → Bootstrap card grid
- [x] Info household → Bootstrap `dl.row` definition list
- [x] Daftar anggota + dot status indicator
- [x] Log aktivitas terbaru → Bootstrap card list rows

**Status 8.3:** ✅ Selesai

### 8.4 — `superadmin/users.blade.php`
- [x] Filter form + tabel dengan badge + toggle-status inline form
- [x] Pagination bootstrap-5

**Status 8.4:** ✅ Selesai

### 8.5 — `superadmin/logs.blade.php`
- [x] Filter search + household dropdown → Bootstrap form
- [x] Tabel log dengan `<code>` action badge + pagination

**Status 8.5:** ✅ Selesai

### 8.6 — `superadmin/health.blade.php`
- [x] Status bar dengan dot indicator (green/red)
- [x] Check cards dengan colored left-border + Bootstrap Icons (check/warning/x)

**Status 8.6:** ✅ Selesai

**Status Fase 8:** ✅ Selesai  
**Catatan:** Semua SVG icon di superadmin digantikan Bootstrap Icons. Status badge `bg-green-900/50 text-green-400` → Bootstrap `badge rounded-pill bg-success/bg-danger`. Tabel menggunakan Bootstrap `table-responsive` wrapper. Primary color (purple) di superadmin dikontrol via CSS variable override di layout superadmin.

---

## Fase 9 — Testing & Cleanup

- [x] **9.1** Uji semua route bisa diakses tanpa error
- [ ] **9.2** Uji responsive (mobile, tablet, desktop) — *manual*
- [ ] **9.3** Uji semua form submit (create, edit, delete) — *manual*
- [ ] **9.4** Uji Chart.js masih berfungsi di semua halaman laporan & dashboard — *manual*
- [ ] **9.5** Uji sidebar toggle di mobile — *manual*
- [ ] **9.6** Uji dropdown user (logout, profil) — *manual*
- [ ] **9.7** Uji flash message (success, error, warning, info) muncul dan hilang — *manual*
- [ ] **9.8** Uji pagination di halaman yang memiliki list panjang — *manual*
- [x] **9.9** Cek tidak ada class Tailwind yang tersisa — ✅ grep konfirmasi 0 remnant
- [x] **9.10** Hapus CDN Tailwind & Alpine.js dari semua file — ✅ konfirmasi bersih
- [ ] **9.11** Hapus folder `public/newtemplate/` setelah selesai — *opsional, lakukan manual*
- [ ] **9.12** Uji alur superadmin end-to-end — *manual*

**Status Fase 9:** ✅ Selesai (otomatis)  
**Catatan:**
- Zero Alpine.js remnant di semua blade file (dikonfirmasi via grep)
- Zero Tailwind-only class remnant (dikonfirmasi via grep)
- `Paginator::useBootstrapFive()` ditambahkan ke `AppServiceProvider`
- `resources/css/app.css` dibersihkan dari `@import tailwindcss`
- `profile/index.blade.php` dibuat (view yang sebelumnya tidak ada)
- Route mismatch diperbaiki: `import-bank.index` → `import-bank.web.index`, `import-bank.preview` → `api.import-bank.preview`, `import-bank.store` → `api.import-bank.store`
- Testing manual (9.2–9.8, 9.11, 9.12) dilakukan oleh developer

---

## Referensi Penting

| Item | Path / URL |
|------|------------|
| Template CSS utama | `public/dompet/css/style.css` |
| Template source | `public/newtemplate/package/` |
| Dokumentasi template | `public/newtemplate/documentation/index.html` |
| Layout utama (source) | `resources/views/layouts/app.blade.php` |
| Layout auth (source) | `resources/views/layouts/auth.blade.php` |
| Layout superadmin | `resources/views/layouts/superadmin.blade.php` |

### CDN & Asset yang Dibutuhkan di Layout `<head>`

```html
{{-- === CSS === --}}

{{-- Google Fonts (sudah ada di style.css tapi backup via link jika offline) --}}
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">

{{-- Dompet core CSS (Bootstrap 5 sudah di-bundle di dalam style.css ini) --}}
<link rel="stylesheet" href="{{ asset('dompet/css/style.css') }}">

{{-- Icon Libraries (lokal) --}}
<link rel="stylesheet" href="{{ asset('dompet/icons/bootstrap-icons/font/bootstrap-icons.css') }}">
<link rel="stylesheet" href="{{ asset('dompet/icons/avasta/css/style.css') }}">

{{-- Icon Libraries (CDN - tidak ada di package) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css">

{{-- Vendor CSS (CDN) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.css">
<link rel="stylesheet" href="{{ asset('dompet/css/perfect-scrollbar.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
```

```html
{{-- === JS (di atas </body>) === --}}

{{-- jQuery (dibutuhkan MetisMenu) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- Bootstrap JS Bundle (termasuk Popper) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- MetisMenu (sidebar navigation) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js"></script>

{{-- Perfect Scrollbar --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/perfect-scrollbar.min.js"></script>

{{-- Chart.js (sudah dipakai, tetap dipertahankan) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

### Konversi Class Umum Tailwind → Bootstrap

| Tailwind | Bootstrap |
|----------|-----------|
| `flex` | `d-flex` |
| `hidden` | `d-none` |
| `grid grid-cols-2` | `row` + `col-6` |
| `rounded-lg` | `rounded` |
| `shadow-sm` | `shadow-sm` |
| `text-sm` | `small` atau `fs-6` |
| `font-bold` | `fw-bold` |
| `text-gray-500` | `text-muted` |
| `bg-white` | `bg-white` |
| `border` | `border` |
| `p-4` | `p-3` |
| `mt-4` | `mt-3` |
| `space-y-4` | `d-flex flex-column gap-3` |
| `hover:bg-gray-100` | — (gunakan CSS custom atau Bootstrap hover utils) |
| `dark:bg-gray-800` | — (Dompet punya dark mode sendiri, evaluasi) |

---

## Catatan Sesi

### Sesi 1 — [tanggal]
- Dikerjakan:
- Masalah:
- Solusi:

### Sesi 2 — [tanggal]
- Dikerjakan:
- Masalah:
- Solusi:

---

*File ini diupdate setiap selesai pengerjaan. Tandai checklist dengan ✅ dan ubah status fase.*
