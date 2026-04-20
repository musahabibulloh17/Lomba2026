# Setup PostgreSQL untuk Laravel

## Langkah-langkah Setup

### 1. Aktifkan PostgreSQL Extension di PHP

Buka file `C:\laragon\bin\php\php-8.4.2-nts-Win32-vs17-x64\php.ini`

Cari dan uncomment baris berikut (hapus `;` di depannya):
```ini
;extension=pdo_pgsql
;extension=pgsql
```

Menjadi:
```ini
extension=pdo_pgsql
extension=pgsql
```

### 2. Restart Laragon/Apache/Web Server

Setelah mengubah php.ini, restart web server Anda.

### 3. Buat Database PostgreSQL

Jalankan perintah berikut di psql atau pgAdmin:

```sql
CREATE DATABASE workflow_ai_db;
```

Atau gunakan Laragon PostgreSQL Manager.

### 4. Konfigurasi Environment

File `.env` di `laravel-app` sudah dikonfigurasi:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=workflow_ai_db
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan kredensial PostgreSQL Anda.

### 5. Jalankan Migrations

```bash
cd laravel-app
php artisan migrate:fresh
```

## Verifikasi

Setelah migration berhasil, Anda akan melihat tables berikut di PostgreSQL:

- users
- tasks
- meetings
- email_logs
- nlp_commands
- password_reset_tokens
- sessions
- cache
- cache_locks
- jobs
- job_batches
- failed_jobs

## Troubleshooting

### Error: "could not find driver"
- Pastikan extension pdo_pgsql dan pgsql sudah di-uncomment di php.ini
- Restart web server setelah mengubah php.ini

### Error: "SQLSTATE[08006]"
- Pastikan PostgreSQL service sudah running
- Cek apakah port 5432 sudah benar
- Cek username dan password di .env

### Error: "database does not exist"
- Buat database terlebih dahulu dengan `CREATE DATABASE workflow_ai_db;`
