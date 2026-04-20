# 🚨 PENTING: Aktifkan PostgreSQL Extension

## Langkah-langkah WAJIB sebelum menjalankan Laravel:

### 1️⃣ Buka File php.ini
Lokasi file: `C:\laragon\bin\php\php-8.4.2-nts-Win32-vs17-x64\php.ini`

**Cara mudah:**
- Buka Laragon → Menu → PHP → php.ini
- Atau buka langsung file tersebut dengan text editor

### 2️⃣ Cari dan Uncomment Extension PostgreSQL

Cari baris berikut (tekan Ctrl+F dan cari "pgsql"):

```ini
;extension=pdo_pgsql
;extension=pgsql
```

**Hapus tanda `;` di depannya** sehingga menjadi:

```ini
extension=pdo_pgsql
extension=pgsql
```

### 3️⃣ Save File php.ini

### 4️⃣ Restart Laragon
- Stop semua services di Laragon
- Start kembali

### 5️⃣ Verifikasi Extension Aktif

Jalankan command ini di terminal:

```bash
php -m | findstr pgsql
```

Harusnya muncul:
```
pdo_pgsql
pgsql
```

### 6️⃣ Pastikan PostgreSQL Service Running

Di Laragon, pastikan PostgreSQL sudah running.

### 7️⃣ Buat Database (jika belum ada)

Buka pgAdmin atau Laragon → Database → PostgreSQL → Create Database

```sql
CREATE DATABASE workflow_ai_db;
CREATE USER workflow_user WITH PASSWORD 'konfirmasi17';
GRANT ALL PRIVILEGES ON DATABASE workflow_ai_db TO workflow_user;
```

Atau gunakan user postgres default jika lebih mudah.

---

## 🔧 Setelah Extension Aktif, Jalankan:

```bash
cd laravel-app

# Test koneksi database
php artisan db:show

# Jalankan migrations
php artisan migrate:fresh

# Jalankan seeder (optional)
php artisan db:seed

# Start development server
php artisan serve
```

---

## ⚠️ Troubleshooting

### Error: "could not find driver"
❌ PostgreSQL extension belum aktif
✅ Ulangi langkah 1-5 di atas

### Error: "SQLSTATE[08006]"
❌ PostgreSQL service tidak running
✅ Start PostgreSQL di Laragon

### Error: "SQLSTATE[08006] Connection refused"
❌ Port atau host salah
✅ Cek .env: DB_HOST=localhost, DB_PORT=5432

### Error: "database does not exist"
❌ Database belum dibuat
✅ Buat database: `CREATE DATABASE workflow_ai_db;`

### Error: "password authentication failed"
❌ Username/password salah
✅ Cek credentials di .env atau gunakan user postgres

---

## 🎯 Quick Fix Alternative

Jika ingin cepat, ubah di `.env` menggunakan user postgres default:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=workflow_ai_db
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Lalu buat database dengan user postgres.

---

**INGAT: Extension PHP harus diaktifkan dulu, tidak ada cara lain!**
