# 🎉 Setup FinanKu - Aplikasi Manajemen Keuangan Keluarga

## ✅ Yang Sudah Selesai

### 1. **Database Structure** (23 Tabel)
- ✅ Plans (paket langganan)
- ✅ Households (rumah tangga/keluarga)
- ✅ Users (dengan household support)
- ✅ Sumber Transaksi (kas, bank, e-wallet)
- ✅ Kategori (dengan parent-child)
- ✅ Transaksi (dengan tags & transfer)
- ✅ Recurring Transaksi
- ✅ Anggaran
- ✅ Tabungan & Tabungan Transaksi
- ✅ Hutang Piutang & Pembayaran
- ✅ Notifikasi
- ✅ Laporan
- ✅ Settings
- ✅ Audit Log
- ✅ Import Bank
- ✅ OCR History
- ✅ AI Insights
- ✅ Backup & Restore
- ✅ Household Invitations
- ✅ Payment History
- ✅ Tags

### 2. **Models** (19 Models dengan Relasi Lengkap)
- ✅ Plan
- ✅ Household
- ✅ User (updated dengan relasi)
- ✅ SumberTransaksi
- ✅ Kategori
- ✅ Transaksi
- ✅ RecurringTransaksi
- ✅ Anggaran
- ✅ Tabungan
- ✅ TabunganTransaksi
- ✅ HutangPiutang
- ✅ HutangPiutangPembayaran
- ✅ Notifikasi
- ✅ Laporan
- ✅ Setting
- ✅ AuditLog
- ✅ OcrHistory
- ✅ HouseholdInvitation
- ✅ PaymentHistory
- ✅ Tag

### 3. **Traits** (3 Traits)
- ✅ BelongsToHousehold (auto-assign & global scope)
- ✅ HasAuditLog (automatic audit trail)
- ✅ HasSoftDelete (soft delete dengan helper)

### 4. **Seeders** (4 Seeders)
- ✅ PlanSeeder (Free, Basic, Premium)
- ✅ SettingSeeder (global settings)
- ✅ SumberTransaksiSeeder (template)
- ✅ KategoriDefaultSeeder (template)

### 5. **Configuration**
- ✅ .env file (MySQL configuration)
- ✅ Database: finanku
- ✅ Locale: Indonesia (id)

---

## 🚀 Langkah Setup (Manual)

Karena ada issue dengan environment path, silakan jalankan command berikut **secara manual di terminal**:

### 1. Buka Terminal Baru di Folder Finanku
```bash
cd C:\laragon\www\Finanku
```

### 2. Generate Application Key
```bash
php artisan key:generate
```

### 3. Buat Database MySQL
Buka **Laragon** → Klik **Database** → Buka **HeidiSQL** atau **phpMyAdmin**, lalu jalankan:
```sql
CREATE DATABASE IF NOT EXISTS finanku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau via command line (jika mysql ada di PATH):
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS finanku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 4. Jalankan Migrations
```bash
php artisan migrate:fresh
```

### 5. Jalankan Seeders
```bash
php artisan db:seed
```

### 6. (Opsional) Jalankan Migrations + Seeders Sekaligus
```bash
php artisan migrate:fresh --seed
```

### 7. Setup Virtual Host (Opsional)
Di Laragon:
1. Klik kanan icon Laragon di system tray
2. Pilih **Apache** → **sites-enabled** → **Add**
3. Nama: `finanku`
4. Path: `C:\laragon\www\Finanku\public`
5. Restart Apache

Akses via: `http://finanku.test`

---

## 📊 Struktur Database

### **Core Tables**
- `plans` - Paket langganan (Free, Basic, Premium)
- `households` - Data rumah tangga/keluarga
- `users` - User dengan role (owner, admin, member)

### **Transaction Tables**
- `sumber_transaksi` - Sumber dana (kas, bank, e-wallet)
- `kategori` - Kategori pemasukan/pengeluaran (hierarchical)
- `transaksi` - Transaksi utama (dengan soft delete & audit log)
- `recurring_transaksi` - Transaksi berulang otomatis
- `tags` - Tag untuk transaksi
- `transaksi_tags` - Pivot table

### **Budget & Savings**
- `anggaran` - Budget per kategori per periode
- `tabungan` - Target tabungan
- `tabungan_transaksi` - Riwayat setor/tarik tabungan

### **Debt Management**
- `hutang_piutang` - Data hutang/piutang
- `hutang_piutang_pembayaran` - Riwayat pembayaran

### **System Tables**
- `notifikasi` - Notifikasi untuk user
- `laporan` - Generated reports
- `settings` - Global & household settings
- `audit_log` - Audit trail semua perubahan
- `import_bank` - Import dari bank statement
- `ocr_history` - Riwayat OCR struk
- `ai_insights` - AI-generated insights
- `backup_restore` - Backup history
- `household_invitations` - Undangan anggota household
- `payment_history` - Riwayat pembayaran subscription

---

## 🎯 Fitur Utama

### 1. **Multi-Household Support**
- Satu user bisa join ke satu household
- Household memiliki owner, admin, dan member
- Data terpisah per household (via global scope)

### 2. **Comprehensive Transaction Management**
- Pemasukan, Pengeluaran, Transfer
- Recurring transactions (otomatis)
- Tags untuk kategorisasi tambahan
- Soft delete dengan audit trail

### 3. **Budget & Savings**
- Budget per kategori per periode (bulanan/tahunan)
- Notifikasi threshold budget
- Target tabungan dengan tracking progress

### 4. **Debt Management**
- Track hutang dan piutang
- Riwayat pembayaran
- Status dan jatuh tempo

### 5. **Advanced Features**
- OCR untuk scan struk belanja
- Import dari bank statement
- AI insights untuk analisis keuangan
- Backup & restore data
- Audit log lengkap

### 6. **Subscription Plans**
- **Free**: 3 anggota, 100 transaksi/bulan, 10 OCR
- **Basic**: 5 anggota, 500 transaksi/bulan, 50 OCR, Rp 29.000/bulan
- **Premium**: Unlimited, Rp 79.000/bulan

---

## 🔧 Next Steps (Development)

1. **Authentication & Authorization**
   - Setup Laravel Breeze/Jetstream
   - Implement role-based access control
   - Household invitation system

2. **API Development**
   - RESTful API untuk semua resources
   - API authentication (Sanctum)
   - Rate limiting per plan

3. **Frontend Development**
   - Dashboard dengan statistik
   - Form transaksi (dengan autocomplete)
   - Budget tracking & visualization
   - Laporan & export (PDF, Excel)

4. **Advanced Features**
   - OCR integration (Tesseract/Cloud Vision)
   - Bank import parser
   - AI insights (OpenAI/local model)
   - Recurring transaction scheduler (cron job)
   - Notification system (email, push)

5. **Testing**
   - Unit tests untuk models
   - Feature tests untuk API
   - Browser tests untuk UI

---

## 📝 Notes

- Semua model sudah memiliki relasi lengkap
- Traits sudah diimplementasikan untuk reusability
- Migrations sudah terurut dengan benar (foreign keys)
- Seeders sudah siap dengan data default

**Happy Coding! 🚀**
