# 🔔 WhatsApp Reminder Setup Guide

## Fitur WhatsApp Notification

Sistem akan mengirim notifikasi WhatsApp otomatis ke user ketika:
- ⏰ Task deadline kurang dari 30 menit
- 📅 Meeting akan dimulai dalam 30 menit

## Setup Configuration

### 1. Konfigurasi Fonnte (Sudah Selesai ✅)

File `.env` sudah dikonfigurasi:
```env
FONNTE_TOKEN=UNjVdR47zG6gESFW7vDr
FONNTE_ENABLED=true
```

### 2. Setup Nomor WhatsApp User

User harus menambahkan nomor WhatsApp mereka ke profile. Ada 2 cara:

#### Cara 1: Via Database (Temporary)
```sql
UPDATE users 
SET phone_number = '081234567890', 
    whatsapp_notifications = true 
WHERE email = 'user@example.com';
```

#### Cara 2: Via UI (Recommended - Needs Implementation)
Tambahkan form di halaman Settings untuk user input nomor HP mereka.

## Running Reminder System

### Manual Test
Test kirim reminder sekarang:
```bash
php artisan reminders:whatsapp
```

### Automatic Scheduler (Recommended)

#### Option A: Laravel Scheduler (Windows)
Jalankan scheduler terus menerus:
```bash
php artisan schedule:work
```

Atau jalankan sekali untuk test:
```bash
php artisan schedule:run
```

#### Option B: Windows Task Scheduler
1. Buka **Task Scheduler**
2. Create Basic Task:
   - Name: "Laravel WhatsApp Reminder"
   - Trigger: Every 5 minutes
   - Action: Start a program
   - Program: `php`
   - Arguments: `artisan reminders:whatsapp`
   - Start in: `C:\Users\MUSA\Documents\GitHub\Lomba2026\laravel-app`

#### Option C: While Development
Jalankan di terminal terpisah:
```bash
# Terminal 1: PHP Server
php artisan serve

# Terminal 2: Scheduler
php artisan schedule:work
```

## Format Nomor Telepon

Nomor HP harus dalam format Indonesia:
- ✅ `081234567890` (akan otomatis jadi 6281234567890)
- ✅ `6281234567890` 
- ✅ `+6281234567890`
- ❌ `0812-3456-7890` (akan dibersihkan otomatis)

## Testing

### 1. Set Nomor HP User
```sql
UPDATE users 
SET phone_number = '081234567890', 
    whatsapp_notifications = true 
WHERE id = 'your-user-id';
```

### 2. Buat Task dengan Deadline Dekat
Via AI Chat:
```
Buatkan task meeting preparation besok jam 14:30
```

Atau via SQL (untuk test cepat):
```sql
INSERT INTO tasks (id, user_id, title, description, due_date, priority, status, created_at, updated_at)
VALUES (
    gen_random_uuid(),
    'your-user-id',
    'Test WhatsApp Reminder',
    'Testing reminder system',
    NOW() + INTERVAL '25 minutes',
    'high',
    'pending',
    NOW(),
    NOW()
);
```

### 3. Jalankan Command
```bash
php artisan reminders:whatsapp
```

### 4. Check Log
```bash
tail -f storage/logs/laravel.log
```

## Message Format

### Task Reminder
```
🔔 *Task Reminder - FlowSpec AI*

📋 *Task:* Meeting preparation
📝 *Description:* Prepare slides and documents
⏰ *Due:* 21 Feb 2026, 14:30
⚠️ *Time Left:* 25 minutes
🔴 *Priority:* High

Please complete this task soon! 💪
```

### Meeting Reminder
```
🔔 *Meeting Reminder - FlowSpec AI*

📅 *Meeting:* Team Standup
📝 *Description:* Daily sync meeting
⏰ *Start:* 21 Feb 2026, 09:00
⚠️ *Starts in:* 15 minutes
🔗 *Link:* https://meet.google.com/abc-defg-hij

Don't be late! 🏃‍♂️
```

## Troubleshooting

### Problem: WhatsApp tidak terkirim

1. **Check Fonnte Token**
   ```bash
   # Test API token
   curl -X POST https://api.fonnte.com/send \
   -H "Authorization: UNjVdR47zG6gESFW7vDr" \
   -d "target=6281234567890" \
   -d "message=Test message"
   ```

2. **Check User Phone Number**
   ```sql
   SELECT id, name, email, phone_number, whatsapp_notifications 
   FROM users 
   WHERE phone_number IS NOT NULL;
   ```

3. **Check Logs**
   ```bash
   tail -100 storage/logs/laravel.log | grep -i "whatsapp"
   ```

### Problem: Reminder terkirim berkali-kali

Sistem menggunakan cache untuk prevent duplicate. Jika masih terjadi, clear cache:
```bash
php artisan cache:clear
```

### Problem: Scheduler tidak jalan

Windows tidak support cron. Gunakan salah satu:
1. `php artisan schedule:work` (keep running)
2. Windows Task Scheduler
3. Jalankan manual: `php artisan reminders:whatsapp`

## Production Deployment

### Recommended Setup:

1. **PM2 atau Supervisor** untuk keep scheduler running:
```bash
# Install PM2 globally
npm install -g pm2

# Start scheduler
pm2 start "php artisan schedule:work" --name "laravel-scheduler"

# Save and auto-start on reboot
pm2 save
pm2 startup
```

2. **Or use NSSM** (Windows Service):
```bash
# Download NSSM from nssm.cc
nssm install LaravelScheduler "php" "artisan schedule:work"
nssm set LaravelScheduler AppDirectory "C:\path\to\laravel-app"
nssm start LaravelScheduler
```

## Security Notes

- ✅ Nomor HP tidak di-expose di API public
- ✅ User bisa disable notifikasi via `whatsapp_notifications` field
- ✅ Token Fonnte disimpan di `.env` (jangan commit!)
- ✅ Rate limiting otomatis via cache (1 reminder per 25 menit)

## Next Steps

1. ✅ Migration phone_number field - DONE
2. ✅ FonnteService created - DONE
3. ✅ Command reminder created - DONE
4. ✅ Scheduler configured - DONE
5. ⏳ UI untuk user input phone number - TODO
6. ⏳ Settings page untuk enable/disable notifikasi - TODO

## Update User Phone Number (Current Workaround)

Untuk saat ini, update manual via SQL:
```sql
-- Update your phone number
UPDATE users 
SET phone_number = '081234567890',  -- Ganti dengan nomor Anda
    whatsapp_notifications = true
WHERE email = 'razzahran305@gmail.com';  -- Ganti dengan email Anda
```
