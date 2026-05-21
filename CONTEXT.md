# CONTEXT.md — Konvensi & Standar Coding FinanKu
# Wajib dibaca dan diikuti oleh Claude Code di setiap sesi
# Versi: 1.0.0

---

## INSTRUKSI UNTUK CLAUDE CODE

Baca file ini di awal SETIAP sesi baru sebelum menulis kode apapun.
Semua konvensi di sini WAJIB diikuti tanpa pengecualian.
Jika ada konflik antara file ini dan instruksi ad-hoc, file ini yang menang.

---

## 1. NAMING CONVENTIONS

### PHP / Laravel
```
Classes      : PascalCase          → TransaksiController, GeminiService
Methods      : camelCase           → uploadStruk(), getSuggest()
Variables    : camelCase           → $namaFile, $totalHarga
Constants    : UPPER_SNAKE_CASE    → MAX_FILE_SIZE, DEFAULT_TIMEOUT
Interfaces   : PascalCase + Interface → BankParserInterface
Traits       : PascalCase          → BelongsToHousehold
Exceptions   : PascalCase + Exception → GeminiException
```

### Database
```
Tabel        : snake_case plural   → transaksi, transaksi_items, toko_pola_items
Kolom        : snake_case          → nama_asli, harga_satuan, is_active
Primary key  : id (bigint)
Foreign key  : {tabel_singular}_id → toko_id, rekening_id, household_id
Pivot tabel  : {tabel1}_{tabel2} alphabetical → transaksi_tags
Timestamp    : created_at, updated_at, deleted_at
Boolean      : is_{sesuatu}        → is_active, is_read, is_suggested
```

### Routes & URLs
```
Route names  : kebab-case          → transaksi.upload-struk, savings-goals.add-fund
URL slugs    : kebab-case          → /savings-goals, /laporan/bulanan
API prefix   : /api/v1/
```

### Files & Folders
```
Controllers  : PascalCase + Controller.php  → TransaksiController.php
Models       : PascalCase singular.php      → Transaksi.php
Services     : PascalCase + Service.php     → GeminiService.php
Middleware   : PascalCase + .php            → HouseholdMiddleware.php
Requests     : {Action}{Model}Request.php   → StoreTransaksiRequest.php
Views        : kebab-case.blade.php         → upload-struk.blade.php
Migrations   : {timestamp}_{action}_{table} → 2024_01_01_000001_create_plans_table
```

---

## 2. STRUKTUR FOLDER LENGKAP

```
app/
├── Console/
│   └── Commands/                  # Artisan commands jika dibutuhkan
├── Exceptions/
│   ├── GeminiException.php
│   └── GeminiLimitException.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── RegisterController.php
│   │   │   ├── LoginController.php
│   │   │   └── PasswordController.php
│   │   ├── Api/
│   │   │   └── V1/
│   │   │       ├── TransaksiController.php
│   │   │       ├── DashboardController.php
│   │   │       └── LaporanController.php
│   │   ├── DashboardController.php
│   │   ├── TransaksiController.php
│   │   ├── ImportController.php
│   │   ├── LaporanController.php
│   │   ├── BudgetController.php
│   │   ├── SavingsGoalController.php
│   │   ├── RecurringController.php
│   │   ├── HouseholdController.php
│   │   ├── RekenungController.php
│   │   ├── KategoriController.php
│   │   ├── TagController.php
│   │   ├── SettingsController.php
│   │   ├── NotificationController.php
│   │   ├── OnboardingController.php
│   │   ├── CronController.php
│   │   └── SuperadminController.php
│   ├── Middleware/
│   │   ├── HouseholdMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   ├── CheckPlanLimit.php
│   │   ├── LogActivity.php
│   │   ├── CronSecret.php
│   │   └── SuperadminGlobal.php
│   └── Requests/
│       ├── StoreTransaksiRequest.php
│       ├── UpdateTransaksiRequest.php
│       ├── StoreRekenungRequest.php
│       ├── StoreBudgetRequest.php
│       ├── StoreSavingsGoalRequest.php
│       ├── StoreRecurringRequest.php
│       ├── UpdateProfileRequest.php
│       ├── UpdatePasswordRequest.php
│       ├── UpdateHouseholdRequest.php
│       ├── JoinHouseholdRequest.php
│       └── ImportFileRequest.php
├── Models/
│   ├── User.php
│   ├── Household.php
│   ├── HouseholdMember.php
│   ├── Plan.php
│   ├── Subscription.php
│   ├── Transaksi.php
│   ├── TransaksiItem.php
│   ├── Toko.php
│   ├── TokoPola.php
│   ├── Rekening.php
│   ├── Kategori.php
│   ├── Budget.php
│   ├── SavingsGoal.php
│   ├── RecurringTransaction.php
│   ├── Tag.php
│   ├── ActivityLog.php
│   ├── Notification.php
│   ├── Setting.php
│   └── Payment.php
├── Observers/
│   ├── TransaksiObserver.php
│   └── UserObserver.php
├── Policies/
│   ├── TransaksiPolicy.php
│   ├── HouseholdPolicy.php
│   └── LaporanPolicy.php
├── Services/
│   ├── GeminiService.php
│   ├── DedupService.php
│   ├── OCRService.php
│   ├── ImageService.php
│   ├── TokoPolaService.php
│   ├── BankMutasiImportService.php
│   ├── LaporanService.php
│   ├── BudgetService.php
│   ├── InsightService.php
│   ├── AnomalyDetectionService.php
│   ├── ExportService.php
│   ├── NotificationService.php
│   ├── PlanLimitService.php
│   ├── ReferralService.php
│   ├── BankMutasiParser/
│   │   ├── BankParserInterface.php
│   │   ├── BCAParser.php
│   │   ├── MandiriParser.php
│   │   ├── BNIParser.php
│   │   ├── BSIParser.php
│   │   └── GenericParser.php
│   └── MarketplaceParser/
│       ├── ShopeeParser.php
│       └── TiktokShopParser.php
└── Traits/
    ├── BelongsToHousehold.php
    ├── HasAuditLog.php
    └── HasSoftDelete.php

database/
├── migrations/                    # 23 file migration, urut 001-023
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── PlanSeeder.php
│   ├── SumberTransaksiSeeder.php
│   ├── SettingSeeder.php
│   └── KategoriDefaultSeeder.php

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   ├── auth.blade.php
    │   └── superadmin.blade.php
    ├── components/
    │   ├── stat-card.blade.php
    │   ├── transaction-row.blade.php
    │   ├── suggest-detail.blade.php
    │   ├── duplikat-modal.blade.php
    │   ├── budget-progress.blade.php
    │   ├── empty-state.blade.php
    │   ├── confirm-modal.blade.php
    │   └── file-upload.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── onboarding/
    │   ├── index.blade.php        # layout steps
    │   ├── step-1-household.blade.php
    │   ├── step-2-rekening.blade.php
    │   ├── step-3-budget.blade.php
    │   ├── step-4-recurring.blade.php
    │   └── step-5-invite.blade.php
    ├── dashboard.blade.php
    ├── transaksi/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── show.blade.php
    │   └── edit.blade.php
    ├── import/
    │   ├── form.blade.php
    │   └── preview.blade.php
    ├── laporan/
    │   ├── index.blade.php
    │   ├── harian.blade.php
    │   ├── mingguan.blade.php
    │   └── bulanan.blade.php
    ├── budget/
    │   └── index.blade.php
    ├── savings-goals/
    │   ├── index.blade.php
    │   └── create.blade.php
    ├── recurring/
    │   └── index.blade.php
    ├── rekening/
    │   └── index.blade.php
    ├── household/
    │   ├── index.blade.php
    │   └── members.blade.php
    ├── settings/
    │   └── index.blade.php
    ├── notifications/
    │   └── index.blade.php
    ├── superadmin/
    │   ├── dashboard.blade.php
    │   ├── households.blade.php
    │   ├── household-show.blade.php
    │   ├── users.blade.php
    │   ├── logs.blade.php
    │   └── health.blade.php
    └── landing/
        └── index.blade.php        # kosong dulu

routes/
├── web.php
├── api.php
└── superadmin.php                 # (bisa digabung ke web.php)

docs/
├── DEPLOYMENT.md
├── SAAS_ROADMAP.md
├── DATABASE.md
├── API.md
└── CHANGELOG.md
```

---

## 3. POLA KODE YANG WAJIB DIIKUTI

### Controller Pattern
```php
// Controller HANYA bertanggung jawab:
// 1. Validasi input (via Form Request)
// 2. Panggil Service
// 3. Return response (view atau JSON)
// JANGAN taruh business logic di Controller

class TransaksiController extends Controller
{
    public function __construct(
        private GeminiService $gemini,
        private DedupService $dedup,
        private TokoPolaService $tokoPola,
        private NotificationService $notifikasi,
        private AnomalyDetectionService $anomaly,
    ) {}

    public function store(StoreTransaksiRequest $request): RedirectResponse
    {
        // 1. Data sudah tervalidasi oleh Form Request
        $data = $request->validated();

        // 2. Panggil service
        $result = $this->tokoPola->findOrCreateToko($data['nama_toko'], auth()->user()->household_id);

        // 3. Dedup check
        $dedup = $this->dedup->checkDuplicate(...);
        if ($dedup) {
            return back()->with('warning', 'Kemungkinan transaksi duplikat ditemukan.');
        }

        // 4. Simpan
        $transaksi = Transaksi::create($data);

        // 5. Side effects
        $this->anomaly->checkNewTransaction($transaksi);
        $this->tokoPola->updatePola($result->id, $transaksi->household_id, $data['items']);

        return redirect()->route('transaksi.show', $transaksi)
            ->with('success', 'Transaksi berhasil disimpan.');
    }
}
```

### Service Pattern
```php
// Service bertanggung jawab untuk semua business logic
// Return array dengan format konsisten

class LaporanService
{
    public function bulanan(int $bulan, int $tahun, int $household_id): array
    {
        // Query data
        $transaksi = Transaksi::where('household_id', $household_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with(['toko', 'kategori', 'items', 'inputBy'])
            ->get();

        // Proses data
        $totalIncome  = $transaksi->where('tipe', 'income')->sum('total');
        $totalOutcome = $transaksi->where('tipe', 'outcome')->sum('total');

        // Return format standar
        return [
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'total_income'  => $totalIncome,
            'total_outcome' => $totalOutcome,
            'cashflow'      => $totalIncome - $totalOutcome,
            'saving_rate'   => $totalIncome > 0 ? (($totalIncome - $totalOutcome) / $totalIncome) * 100 : 0,
            'transaksi'     => $transaksi,
            // ... dst
        ];
    }
}
```

### Model Pattern
```php
class Transaksi extends Model
{
    use BelongsToHousehold, HasAuditLog, SoftDeletes;

    protected $fillable = [
        'household_id', 'toko_id', 'rekening_id', 'kategori_id',
        'sumber_id', 'input_by', 'tipe', 'tanggal', 'total',
        'metode_bayar', 'catatan', 'tags', 'file_path',
        'attachment_paths', 'raw_text', 'hash_dedup', 'sumber_list',
        'is_detail_lengkap', 'is_recurring', 'recurring_id',
        'transfer_ke', 'status',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'total'            => 'decimal:2',
        'tags'             => 'array',
        'attachment_paths' => 'array',
        'sumber_list'      => 'array',
        'is_detail_lengkap' => 'boolean',
        'is_recurring'     => 'boolean',
    ];

    // Relasi
    public function toko(): BelongsTo { return $this->belongsTo(Toko::class); }
    public function items(): HasMany { return $this->hasMany(TransaksiItem::class); }

    // Accessor
    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal->translatedFormat('d F Y');
    }

    // Scopes
    public function scopeIncome($query) { return $query->where('tipe', 'income'); }
    public function scopeOutcome($query) { return $query->where('tipe', 'outcome'); }
    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }
}
```

### View Pattern
```blade
{{-- SELALU gunakan komponen untuk elemen berulang --}}
<x-stat-card
    title="Total Pengeluaran"
    :value="'Rp ' . number_format($totalOutcome, 0, ',', '.')"
    subtitle="Bulan ini"
    color="red"
    icon="arrow-down"
/>

{{-- SELALU escape output kecuali HTML yang sudah aman --}}
{{ $transaksi->nama_toko }}      {{-- aman --}}
{!! $htmlContent !!}             {{-- hanya jika sudah dipastikan aman --}}

{{-- Format angka SELALU Rupiah --}}
Rp {{ number_format($total, 0, ',', '.') }}

{{-- Format tanggal SELALU Bahasa Indonesia --}}
{{ $transaksi->tanggal->translatedFormat('d F Y') }}

{{-- Error message SELALU dari session --}}
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
```

---

## 4. RESPONSE FORMAT STANDAR

### Service Response (untuk operasi yang bisa gagal)
```php
// SUKSES
return [
    'success' => true,
    'data'    => $result,
    'message' => 'Berhasil disimpan.',
];

// GAGAL
return [
    'success' => false,
    'data'    => null,
    'message' => 'Gagal menyimpan: ' . $e->getMessage(),
    'error'   => $e->getMessage(),
];
```

### JSON Response dari Controller (untuk AJAX/API)
```php
// Sukses
return response()->json([
    'success' => true,
    'data'    => $data,
    'message' => 'Berhasil diproses.',
], 200);

// Error validasi
return response()->json([
    'success' => false,
    'errors'  => $validator->errors(),
    'message' => 'Data tidak valid.',
], 422);

// Error server
return response()->json([
    'success' => false,
    'message' => 'Terjadi kesalahan. Silakan coba lagi.',
], 500);
```

### Flash Message (untuk redirect)
```php
// Gunakan konsisten:
->with('success', 'Pesan sukses dalam Bahasa Indonesia.')
->with('error', 'Pesan error dalam Bahasa Indonesia.')
->with('warning', 'Pesan peringatan.')
->with('info', 'Pesan informasi.')
```

---

## 5. ERROR HANDLING

### Try-Catch Wajib Untuk:
```php
// Semua call ke Gemini API
try {
    $result = $this->gemini->ocrAndExtract($base64, $mime);
} catch (GeminiLimitException $e) {
    // Limit harian tercapai
    return back()->with('warning', 'Batas penggunaan OCR hari ini telah tercapai. Silakan input manual.');
} catch (GeminiException $e) {
    // Error API lainnya
    Log::error('Gemini API error: ' . $e->getMessage());
    return back()->with('error', 'Layanan AI sedang tidak tersedia. Silakan input manual.');
}

// Semua operasi file
try {
    $path = $this->ocr->compressAndSave($request->file('struk'));
} catch (\Exception $e) {
    Log::error('File upload error: ' . $e->getMessage());
    return back()->with('error', 'Gagal mengupload file. Pastikan ukuran file tidak melebihi 5MB.');
}

// Semua operasi database yang kompleks
try {
    DB::transaction(function () use ($data) {
        $transaksi = Transaksi::create($data);
        foreach ($data['items'] as $item) {
            $transaksi->items()->create($item);
        }
    });
} catch (\Exception $e) {
    Log::error('Transaksi save error: ' . $e->getMessage());
    return back()->with('error', 'Gagal menyimpan transaksi. Silakan coba lagi.');
}
```

### Logging
```php
// Gunakan channel yang sesuai
Log::info('OCR berhasil', ['transaksi_id' => $id, 'toko' => $nama]);
Log::warning('Duplikat terdeteksi', ['hash' => $hash, 'existing_id' => $id]);
Log::error('Gemini API error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
```

---

## 6. DATABASE BEST PRACTICES

### Query Yang Benar
```php
// BENAR — selalu eager load relasi yang dibutuhkan
$transaksi = Transaksi::with(['toko', 'items', 'kategori', 'inputBy'])
    ->where('household_id', $householdId)
    ->paginate(15);

// SALAH — N+1 problem
$transaksi = Transaksi::where('household_id', $householdId)->get();
foreach ($transaksi as $t) {
    echo $t->toko->nama; // N+1 query!
}

// BENAR — agregasi di database, bukan PHP
$totalIncome = Transaksi::where('household_id', $householdId)
    ->where('tipe', 'income')
    ->whereMonth('tanggal', $bulan)
    ->sum('total');

// SALAH — ambil semua lalu sum di PHP
$semua = Transaksi::where('household_id', $householdId)->get();
$total = $semua->where('tipe', 'income')->sum('total'); // tidak efisien
```

### Transaksi Database
```php
// Gunakan DB::transaction untuk operasi multi-tabel
DB::transaction(function () use ($data, $items) {
    $transaksi = Transaksi::create($data);
    foreach ($items as $item) {
        $transaksi->items()->create($item);
    }
    $this->tokoPola->updatePola($transaksi->toko_id, $transaksi->household_id, $items);
});
```

### Index Database
```php
// Tambahkan index untuk kolom yang sering difilter
$table->index(['household_id', 'tanggal']);
$table->index(['household_id', 'tipe']);
$table->index('hash_dedup');
$table->index('status');
```

---

## 7. KEAMANAN (SECURITY)

### Input Validation — WAJIB
```php
// Selalu gunakan Form Request untuk validasi
// Selalu whitelist field yang diterima (validated())
// Jangan pernah $request->all() langsung ke create/update

class StoreTransaksiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tipe'        => 'required|in:income,outcome,transfer',
            'tanggal'     => 'required|date|before_or_equal:today',
            'total'       => 'required|numeric|min:1|max:999999999',
            'nama_toko'   => 'required|string|max:255',
            'rekening_id' => 'nullable|exists:rekening,id',
            'kategori_id' => 'nullable|exists:kategori,id',
            'catatan'     => 'nullable|string|max:1000',
            'items'       => 'nullable|array',
            'items.*.nama_item'    => 'required_with:items|string|max:255',
            'items.*.qty'          => 'required_with:items|numeric|min:0.01',
            'items.*.harga_satuan' => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'tipe.required'  => 'Jenis transaksi wajib dipilih.',
            'total.required' => 'Jumlah transaksi wajib diisi.',
            'total.min'      => 'Jumlah transaksi minimal Rp 1.',
            // dst...
        ];
    }
}
```

### File Upload Security
```php
// Cek mime type (bukan hanya extension)
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file->getMimeType(), $allowedMimes)) {
    return back()->with('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
}

// Cek ukuran
if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
    return back()->with('error', 'Ukuran file maksimal 5MB.');
}

// Simpan dengan nama acak (UUID), bukan nama asli
$filename = Str::uuid() . '.jpg';
```

### Authorization
```php
// Selalu cek permission di Controller
public function edit(Transaksi $transaksi): View
{
    // Cek: user hanya bisa edit transaksi sendiri (kecuali superadmin household)
    $member = HouseholdMember::where([
        'household_id' => session('active_household_id'),
        'user_id' => auth()->id(),
    ])->first();

    if ($member->role !== 'superadmin' && $transaksi->input_by !== auth()->id()) {
        abort(403, 'Kamu tidak memiliki izin untuk mengedit transaksi ini.');
    }

    return view('transaksi.edit', compact('transaksi'));
}
```

---

## 8. FORMAT ANGKA & TANGGAL

### Angka Rupiah
```php
// PHP (Controller/Service)
'Rp ' . number_format($total, 0, ',', '.')
// Contoh output: "Rp 1.250.000"

// Blade View
Rp {{ number_format($total, 0, ',', '.') }}

// Accessor di Model (gunakan untuk konsistensi)
public function getTotalFormattedAttribute(): string
{
    return 'Rp ' . number_format($this->total, 0, ',', '.');
}
// Penggunaan: $transaksi->total_formatted
```

### Tanggal
```php
// Database: SELALU simpan dalam format Y-m-d
$tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

// Display: SELALU dalam Bahasa Indonesia
// Pastikan locale sudah di-set di AppServiceProvider:
// Carbon::setLocale('id');
// setlocale(LC_TIME, 'id_ID');

$tanggal->translatedFormat('d F Y')    // "10 Mei 2026"
$tanggal->translatedFormat('l, d F Y') // "Minggu, 10 Mei 2026"
$tanggal->translatedFormat('M Y')      // "Mei 2026"

// Relatif
$transaksi->created_at->diffForHumans() // "3 jam yang lalu"
```

### Persentase
```php
// Selalu 1 desimal
number_format($persentase, 1) . '%'
// Contoh: "45.3%"
```

---

## 9. BLADE & FRONTEND

### TailwindCSS CDN (WAJIB — tidak ada build process)
```html
<!-- Di layouts/app.blade.php -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Konfigurasi Tailwind via script jika perlu custom colors -->
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#3B82F6',   // biru
                success: '#10B981',   // hijau
                danger:  '#EF4444',   // merah
                warning: '#F59E0B',   // kuning
            }
        }
    }
}
</script>
```

### Alpine.js CDN (untuk interaktivitas ringan)
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### Chart.js CDN (untuk grafik)
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

### Pola Alpine.js yang Digunakan
```html
<!-- Toggle visibility -->
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Konten</div>
</div>

<!-- Fetch AJAX -->
<div x-data="{ loading: false, result: null }">
    <button @click="
        loading = true;
        fetch('/transaksi/suggest?toko_id=1&total=50000')
            .then(r => r.json())
            .then(data => { result = data; loading = false; })
    ">
        Get Suggest
    </button>
</div>

<!-- Dynamic table rows -->
<div x-data="{ items: [{ nama: '', qty: 1, harga: 0 }] }">
    <template x-for="(item, index) in items" :key="index">
        <div>
            <input x-model="items[index].nama" type="text">
            <button @click="items.splice(index, 1)">Hapus</button>
        </div>
    </template>
    <button @click="items.push({ nama: '', qty: 1, harga: 0 })">+ Tambah</button>
</div>
```

### Dark Mode
```html
<!-- Toggle dark mode via class 'dark' di html element -->
<html x-data="{ dark: localStorage.getItem('theme') === 'dark' }"
      :class="{ 'dark': dark }">

<!-- Dalam CSS Tailwind, gunakan dark: prefix -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
```

---

## 10. KONFIGURASI LARAVEL

### AppServiceProvider.php
```php
public function boot(): void
{
    // Set locale Bahasa Indonesia untuk Carbon
    Carbon::setLocale('id');
    setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

    // Set timezone
    date_default_timezone_set(config('app.timezone'));

    // Force HTTPS di production
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}
```

### Cache Configuration (WAJIB database driver)
```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'database'),
// JANGAN gunakan 'file' (shared hosting bisa ada masalah permission)
// JANGAN gunakan 'redis' (tidak tersedia di shared hosting)
```

### Session Configuration
```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'database'),
```

---

## 11. MULTI-BAHASA (ID)

### Semua teks UI dalam Bahasa Indonesia
```php
// Nama bulan (untuk translatedFormat sudah otomatis)
// Pastikan Carbon locale sudah di-set

// Contoh error message yang baik:
'Transaksi berhasil disimpan.'                          // sukses
'Gagal menyimpan transaksi. Silakan coba lagi.'        // error umum
'File terlalu besar. Maksimal 5MB.'                     // error spesifik
'Layanan AI sedang tidak tersedia. Silakan input manual.' // graceful degradation
'Kemungkinan transaksi duplikat. Periksa kembali.'     // warning
'Kamu tidak memiliki izin untuk aksi ini.'             // unauthorized

// HINDARI:
'Error occurred.'           // terlalu teknis
'500 Internal Server Error' // menakutkan
'Null pointer exception'    // tidak informatif
```

---

## 12. TESTING MANUAL CHECKLIST

Sebelum declare "selesai", pastikan ini berfungsi:

### Auth & Onboarding
- [ ] Register user baru → onboarding berjalan
- [ ] Invite anggota → join dengan kode
- [ ] Login/logout
- [ ] Forgot password

### Transaksi
- [ ] Input manual (income, outcome, transfer)
- [ ] Upload struk foto → OCR berhasil
- [ ] Upload struk foto → OCR gagal → fallback manual
- [ ] Toko baru → tidak ada suggest (normal)
- [ ] Toko dengan history → suggest muncul
- [ ] Edit transaksi (admin milik sendiri)
- [ ] Soft delete (superadmin)

### Import
- [ ] Upload Excel mutasi BCA
- [ ] Preview menampilkan status per baris
- [ ] Baris duplikat terdeteksi
- [ ] Konfirmasi import hanya baris yang dipilih

### Laporan
- [ ] Laporan harian tampil
- [ ] Laporan bulanan dengan filter
- [ ] Export Excel berhasil download
- [ ] Export PDF berhasil download

### Budget & Goals
- [ ] Set budget kategori
- [ ] Progress bar update sesuai transaksi
- [ ] Alert saat mendekati limit

### Multi User
- [ ] Suami input → istri bisa lihat di laporan
- [ ] Viewer tidak bisa input transaksi
- [ ] Data tidak bocor antar household

---

## 13. DEPENDENCY INJECTION

```php
// SELALU gunakan constructor injection
class TransaksiController extends Controller
{
    public function __construct(
        private readonly GeminiService $gemini,
        private readonly DedupService $dedup,
    ) {}
}

// Daftarkan binding di AppServiceProvider jika perlu
// (umumnya Laravel auto-resolve via type hint)
```

---

## 14. KOMANDO ARTISAN YANG SERING DIPAKAI

```bash
# Development
php artisan migrate:fresh --seed    # reset + seed database
php artisan route:list              # cek semua route
php artisan config:clear            # clear config cache
php artisan cache:clear             # clear cache
php artisan view:clear              # clear view cache

# Production (di lokal sebelum deploy)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
composer install --optimize-autoloader --no-dev

# Debugging
php artisan tinker                  # REPL untuk testing
php artisan model:show Transaksi    # lihat info model
```

---

## 15. CHECKLIST SETIAP FILE YANG DIBUAT

Sebelum declare file selesai, pastikan:

- [ ] Namespace benar sesuai lokasi file
- [ ] Semua use statement lengkap (tidak ada yang kurang)
- [ ] Tidak ada syntax error (cek dengan php -l filename.php jika bisa)
- [ ] Model: fillable, casts, relasi semua ada
- [ ] Service: semua method sesuai PROMPT.md, ada try-catch
- [ ] Controller: pakai Form Request, panggil Service (bukan logic langsung)
- [ ] View: tidak ada hardcoded string Inggris, format angka Rupiah
- [ ] Migration: tipe kolom benar (DECIMAL bukan FLOAT untuk uang)
- [ ] PROGRESS.md sudah diupdate
