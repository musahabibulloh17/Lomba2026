# 🚀 Langkah Setelah Restart Laragon

## ✅ Yang Sudah Dilakukan:
1. PostgreSQL extension sudah diaktifkan di php.ini
2. Backup php.ini dibuat di: `C:\laragon\bin\php\php-8.4.2-nts-Win32-vs17-x64\php.ini.backup-20260221-020726`

## 📋 Langkah Selanjutnya:

### 1. RESTART LARAGON (WAJIB!)
- Stop All di Laragon
- Tunggu beberapa detik
- Start All

### 2. Verifikasi Extension Aktif
Setelah restart, jalankan di terminal:
```bash
php -m | Select-String pgsql
```

Harusnya muncul:
```
pdo_pgsql
pgsql
```

### 3. Pastikan Database Sudah Ada
Buka pgAdmin atau Laragon Database Manager, pastikan database `workflow_ai_db` sudah ada.

Jika belum, buat dengan SQL:
```sql
CREATE DATABASE workflow_ai_db;
```

### 4. Test Koneksi Database
```bash
php artisan db:show
```

### 5. Jalankan Migrations
```bash
php artisan migrate:fresh
```

### 6. (Optional) Jalankan Seeder
```bash
php artisan db:seed
```

### 7. Start Laravel Development Server
```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

---

## 🎯 Command Lengkap (Copy-Paste Setelah Restart)

```bash
# Verifikasi extension
php -m | Select-String pgsql

# Test koneksi
php artisan db:show

# Jalankan migrations
php artisan migrate:fresh

# Start server
php artisan serve
```

---

## ⚠️ Jika Masih Error

### Error: "could not find driver"
→ Laragon belum di-restart atau restart belum sempurna
→ Restart lagi Laragon

### Error: "database does not exist"  
→ Buat database terlebih dahulu:
```sql
CREATE DATABASE workflow_ai_db;
```

### Error: "password authentication failed"
→ Cek credentials di `.env`:
```env
DB_USERNAME=workflow_user
DB_PASSWORD=konfirmasi17
```

Atau gunakan user postgres default di `.env`:
```env
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

---

**Silakan restart Laragon dulu, lalu lanjutkan dengan command di atas!** 🚀
