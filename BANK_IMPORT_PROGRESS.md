# Bank Import Feature Progress

## Status
Selesai diimplementasikan dan sudah lolos pengecekan syntax PHP (`php -l`) untuk file-file terkait.

## Fitur yang Ditambahkan

### 1. Model Import Bank
- File: `app/Models/ImportBank.php`
- Mendukung pencatatan riwayat import mutasi bank per household.
- Relasi ke:
  - User
  - Household
  - Sumber Transaksi

### 2. Parser Mutasi Bank
Folder: `app/Services/BankParsers`

Parser yang tersedia:
- `GenericParser`
- `BCAParser`
- `MandiriParser`
- `BNIParser`
- `BSIParser`

File pendukung:
- `BankParserInterface.php`
- `AbstractCsvBankParser.php`

Parser membaca file CSV/TXT dan menormalisasi transaksi menjadi format standar:
- tanggal
- deskripsi
- debit
- kredit
- nominal
- tipe transaksi
- reference/hash untuk deduplikasi

### 3. Service Import Bank
- File: `app/Services/BankImportService.php`

Kemampuan utama:
- Memilih parser berdasarkan `bank_code`
- Preview data mutasi sebelum import
- Import transaksi ke tabel `transaksi`
- Deduplikasi transaksi berdasarkan hash/reference
- Pencatatan jumlah:
  - total row
  - imported row
  - duplicate row
  - failed row
- Menyimpan error per baris jika ada kegagalan parsing/import
- Mencatat status import:
  - `processing`
  - `completed`
  - `failed`

### 4. Form Request
- File: `app/Http/Requests/ImportBankRequest.php`

Validasi:
- `file`: required, file, csv/txt, max 5MB
- `bank_code`: optional, pilihan `generic,bca,mandiri,bni,bsi`
- `sumber_transaksi_id`: required dan harus ada di tabel `sumber_transaksi`
- `kategori_id`: optional dan harus ada di tabel `kategori`

### 5. Controller
- File: `app/Http/Controllers/ImportBankController.php`

Endpoint handler:
- `index()` untuk daftar history import
- `store()` untuk import mutasi bank
- `preview()` untuk preview file mutasi bank
- `show()` untuk detail history import

### 6. Routes
- File: `routes/web.php`

Route API-like di dalam middleware `auth` dan prefix `api`:

```php
Route::prefix('import-bank')->name('import-bank.')->group(function () {
    Route::get('/', [ImportBankController::class, 'index'])->name('index');
    Route::post('/', [ImportBankController::class, 'store'])->name('store');
    Route::post('/preview', [ImportBankController::class, 'preview'])->name('preview');
    Route::get('/{importBank}', [ImportBankController::class, 'show'])->name('show');
});
```

Endpoint:
- `GET /api/import-bank`
- `POST /api/import-bank`
- `POST /api/import-bank/preview`
- `GET /api/import-bank/{importBank}`

## Verifikasi

Syntax PHP sudah diperiksa untuk file:
- `app/Services/BankImportService.php`
- `app/Http/Requests/ImportBankRequest.php`
- `app/Http/Controllers/ImportBankController.php`
- `routes/web.php`
- `app/Services/BankParsers/BankParserInterface.php`
- `app/Services/BankParsers/AbstractCsvBankParser.php`
- `app/Services/BankParsers/GenericParser.php`
- `app/Services/BankParsers/BCAParser.php`
- `app/Services/BankParsers/MandiriParser.php`
- `app/Services/BankParsers/BNIParser.php`
- `app/Services/BankParsers/BSIParser.php`

Hasil:
```text
No syntax errors detected
```

## Catatan Integrasi Frontend

Frontend dapat memakai flow berikut:
1. Upload file ke `POST /api/import-bank/preview` untuk melihat hasil parsing awal.
2. Jika preview sesuai, submit file ke `POST /api/import-bank` dengan:
   - `file`
   - `bank_code`
   - `sumber_transaksi_id`
   - `kategori_id` optional
3. Ambil history melalui `GET /api/import-bank`.
4. Ambil detail import melalui `GET /api/import-bank/{importBank}`.