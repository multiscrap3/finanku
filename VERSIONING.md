# VERSIONING — FinanKu

Panduan lengkap sistem versioning aplikasi FinanKu.

---

## Format Versi: `MAJOR.MINOR.PATCH`

FinanKu menggunakan **Semantic Versioning 2.0.0** ([semver.org](https://semver.org)).

```
  1   .   0   .   0
  │       │       └── PATCH  — bug fix, hotfix, typo, perubahan kecil tanpa fitur baru
  │       └────────── MINOR  — fitur baru yang backward-compatible
  └────────────────── MAJOR  — perubahan breaking, redesign besar, atau milestone utama
```

### Kapan naikkan angka mana?

| Jenis perubahan | Contoh | Naikkan |
|---|---|---|
| Bug fix | Perbaikan kalkulasi saldo | `PATCH` |
| Fitur baru kecil | Tambah filter di laporan | `MINOR` |
| Fitur baru besar | Modul hutang-piutang selesai | `MINOR` |
| Perubahan database schema | Tambah kolom di tabel transaksi | `MINOR` |
| Breaking change | Redesign API, ganti auth system | `MAJOR` |
| Milestone besar | Frontend selesai, app production-ready | `MAJOR` |
| Security patch | Patch XSS / CSRF / SQL injection | `PATCH` atau `MINOR` |

---

## Versi Saat Ini

```
1.0.0
```

File: [`VERSION`](./VERSION)

---

## Roadmap Versi

| Versi | Target | Milestone |
|---|---|---|
| **1.0.0** | 2026-05-18 | Backend Core 54% — Database, Models, Core Services & Controllers |
| **1.1.0** | TBD | Semua Controllers selesai (15/15) |
| **1.2.0** | TBD | Semua Services selesai (10/10) |
| **1.3.0** | TBD | Middleware & Form Requests lengkap |
| **2.0.0** | TBD | Frontend Views selesai — App siap dijalankan |
| **2.1.0** | TBD | Fitur OCR struk belanja |
| **2.2.0** | TBD | Fitur Import Bank (CSV/Excel) |
| **2.3.0** | TBD | Fitur AI Insights |
| **3.0.0** | TBD | Production release — semua fitur lengkap & tested |

---

## File Terkait Versioning

| File | Fungsi |
|---|---|
| [`VERSION`](./VERSION) | Satu baris berisi nomor versi aktif |
| [`CHANGELOG.md`](./CHANGELOG.md) | Log semua perubahan per versi |
| [`VERSIONING.md`](./VERSIONING.md) | Panduan ini |
| `config/app.php` | `'version'` key yang dibaca oleh app |

---

## Cara Update Versi

### 1. Edit file `VERSION`
```
1.1.0
```

### 2. Update `config/app.php`
```php
'version' => env('APP_VERSION', '1.1.0'),
```

### 3. Update `.env` (opsional)
```env
APP_VERSION=1.1.0
```

### 4. Tambahkan entry di `CHANGELOG.md`
```markdown
## [1.1.0] — 2026-XX-XX

### Added
- KategoriController — CRUD kategori dengan validasi per household
- SumberTransaksiController — CRUD sumber transaksi, update saldo manual
...
```

### 5. Update link di bagian bawah `CHANGELOG.md`
```markdown
[1.1.0]: https://github.com/username/finanku/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/username/finanku/releases/tag/v1.0.0
```

---

## Menampilkan Versi di Aplikasi

### Di Blade View
```blade
<span>FinanKu v{{ config('app.version') }}</span>
```

### Di PHP / Controller
```php
$version = config('app.version');  // "1.0.0"
```

### Di API Response
```php
return response()->json([
    'app'     => config('app.name'),
    'version' => config('app.version'),
]);
```

---

## Pre-release & Build Metadata (Opsional)

Untuk keperluan development, bisa menggunakan suffix:

```
1.1.0-alpha.1     — tahap alpha (belum stabil)
1.1.0-beta.2      — tahap beta (fitur lengkap, masih testing)
1.1.0-rc.1        — release candidate (siap rilis, final testing)
1.1.0             — stable release
```

---

*FinanKu — Aplikasi Keuangan Keluarga Multi-User*
*Versi dokumen ini: 1.0.0 — 2026-05-18*
