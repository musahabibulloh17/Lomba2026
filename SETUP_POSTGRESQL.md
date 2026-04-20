# 🐘 Setup PostgreSQL di Windows

## Opsi 1: Install PostgreSQL Lokal (Recommended)

### Download & Install

1. **Download PostgreSQL:**
   - Kunjungi: https://www.postgresql.org/download/windows/
   - Download installer versi 14 atau lebih baru
   - Jalankan installer

2. **Proses Instalasi:**
   - Pilih komponen: PostgreSQL Server, pgAdmin 4, Command Line Tools
   - Port: `5432` (default)
   - Locale: `[Default locale]`
   - **PENTING:** Catat password untuk superuser `postgres`

3. **Setelah Instalasi Selesai:**
   - PostgreSQL akan berjalan sebagai Windows Service
   - pgAdmin 4 akan terinstall untuk GUI management

### Setup Database

Buka **Command Prompt** atau **PowerShell** sebagai Administrator:

```powershell
# Masuk ke PostgreSQL
psql -U postgres

# Di psql prompt, jalankan:
CREATE DATABASE workflow_ai_db;
CREATE USER workflow_user WITH PASSWORD 'workflow_password';
GRANT ALL PRIVILEGES ON DATABASE workflow_ai_db TO workflow_user;

# Keluar dari psql
\q
```

### Update File .env

Buat file `.env` di folder `backend/`:

```env
# Server Configuration
NODE_ENV=development
PORT=5000

# Database Configuration
DB_HOST=localhost
DB_PORT=5432
DB_NAME=workflow_ai_db
DB_USER=workflow_user
DB_PASSWORD=workflow_password

# JWT Configuration
JWT_SECRET=your-secret-key-here
JWT_EXPIRE=7d

# OpenAI Configuration
OPENAI_API_KEY=sk-your-openai-api-key
OPENAI_MODEL=gpt-4

# (Isi konfigurasi lainnya sesuai kebutuhan)
```

### Test Connection

```powershell
cd backend
node src/database/migrate.js
```

Jika berhasil, Anda akan melihat:
```
✅ Database migration completed successfully
```

---

## Opsi 2: Menggunakan Docker Desktop

### Install Docker Desktop

1. **Download:**
   - https://www.docker.com/products/docker-desktop/

2. **Install dan Restart:**
   - Jalankan installer
   - Restart komputer jika diminta
   - Buka Docker Desktop dan tunggu sampai running

### Jalankan PostgreSQL Container

```powershell
# Pull dan jalankan PostgreSQL
docker run -d `
  --name postgres-workflow `
  -e POSTGRES_DB=workflow_ai_db `
  -e POSTGRES_USER=workflow_user `
  -e POSTGRES_PASSWORD=workflow_password `
  -p 5432:5432 `
  postgres:14-alpine

# Check status
docker ps
```

### Update .env

```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=workflow_ai_db
DB_USER=workflow_user
DB_PASSWORD=workflow_password
```

### Test Connection

```powershell
cd backend
node src/database/migrate.js
```

---

## Opsi 3: PostgreSQL Cloud (Free Tier)

### Supabase (Recommended - Free 500MB)

1. **Daftar:**
   - https://supabase.com/
   - Klik "Start your project"

2. **Buat Project:**
   - Pilih region terdekat (Singapore untuk Asia)
   - Set database password (catat password ini!)
   - Tunggu project dibuat (~2 menit)

3. **Get Connection String:**
   - Buka Settings > Database
   - Copy "Connection string" di bagian "Connection pooling"
   - Format: `postgres://postgres.[project-ref]:[password]@aws-0-[region].pooler.supabase.com:6543/postgres`

4. **Update .env:**

```env
# Opsi A: Connection String
DATABASE_URL=postgres://postgres.xxxxx:[YOUR-PASSWORD]@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres

# Opsi B: Individual Parameters
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_NAME=postgres
DB_USER=postgres.xxxxx
DB_PASSWORD=your-password
```

5. **Update connection.js untuk support DATABASE_URL:**

Edit `backend/src/database/connection.js`:

```javascript
const { Sequelize } = require('sequelize');
require('dotenv').config();

let sequelize;

if (process.env.DATABASE_URL) {
  // Use DATABASE_URL if available (for cloud databases)
  sequelize = new Sequelize(process.env.DATABASE_URL, {
    dialect: 'postgres',
    dialectOptions: {
      ssl: {
        require: true,
        rejectUnauthorized: false
      }
    },
    logging: process.env.NODE_ENV === 'development' ? console.log : false,
    pool: {
      max: 10,
      min: 0,
      acquire: 30000,
      idle: 10000
    }
  });
} else {
  // Use individual connection parameters
  sequelize = new Sequelize(
    process.env.DB_NAME,
    process.env.DB_USER,
    process.env.DB_PASSWORD,
    {
      host: process.env.DB_HOST,
      port: process.env.DB_PORT || 5432,
      dialect: 'postgres',
      logging: process.env.NODE_ENV === 'development' ? console.log : false,
      pool: {
        max: 10,
        min: 0,
        acquire: 30000,
        idle: 10000
      },
      define: {
        timestamps: true,
        underscored: true,
        freezeTableName: true
      }
    }
  );
}

module.exports = { sequelize };
```

### ElephantSQL (Free 20MB)

1. **Daftar:**
   - https://www.elephantsql.com/
   - Login dengan GitHub atau email

2. **Create Instance:**
   - Klik "Create New Instance"
   - Plan: Tiny Turtle (Free)
   - Datacenter: pilih yang terdekat

3. **Get Connection URL:**
   - Klik instance yang baru dibuat
   - Copy "URL" connection string

4. **Update .env:**

```env
DATABASE_URL=postgres://username:password@hostname.db.elephantsql.com/database
```

---

## Troubleshooting

### Error: "psql: command not found"

PostgreSQL belum terinstall atau PATH belum di-setup.

**Solusi:**
1. Cari lokasi instalasi PostgreSQL (biasanya `C:\Program Files\PostgreSQL\14\bin`)
2. Tambahkan ke PATH:
   - Start > "Environment Variables"
   - Edit "Path" di System Variables
   - Tambahkan: `C:\Program Files\PostgreSQL\14\bin`
3. Buka Command Prompt baru

### Error: "Connection Refused" (ECONNREFUSED)

PostgreSQL service tidak berjalan.

**Solusi:**
```powershell
# Check service status
Get-Service -Name postgresql*

# Start service
Start-Service postgresql-x64-14
```

Atau melalui Services:
- Start > "services.msc"
- Cari "postgresql-x64-14"
- Klik kanan > Start

### Error: "password authentication failed"

Password salah atau user tidak ada.

**Solusi:**
```powershell
# Reset password
psql -U postgres
ALTER USER workflow_user WITH PASSWORD 'new_password';
```

### Error: "database does not exist"

Database belum dibuat.

**Solusi:**
```powershell
psql -U postgres
CREATE DATABASE workflow_ai_db;
GRANT ALL PRIVILEGES ON DATABASE workflow_ai_db TO workflow_user;
```

### Port 5432 Sudah Digunakan

Ada aplikasi lain yang menggunakan port 5432.

**Solusi:**
```powershell
# Check apa yang menggunakan port 5432
netstat -ano | findstr :5432

# Ubah port di .env ke port lain (misalnya 5433)
DB_PORT=5433
```

---

## Verifikasi Setup

Setelah PostgreSQL ready, test dengan:

```powershell
cd backend
npm install
node src/database/migrate.js
node src/database/seed.js
```

Jika berhasil, Anda akan melihat:
```
✅ Database migration completed successfully
✅ Created 3 users
✅ Created 3 tasks
✅ Created 2 meetings
✅ Database seeding completed successfully
```

## Next Steps

1. ✅ PostgreSQL Running
2. ✅ Database Created
3. ✅ Migration Success
4. 🚀 Start Backend: `npm run dev`
5. 🌐 Start Frontend: `cd ../frontend && npm start`

---

**Butuh bantuan?** Buka GitHub Issues atau hubungi tim support.
