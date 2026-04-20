# 🔔 Cara Mengaktifkan/Menonaktifkan Notifikasi WhatsApp

## Langkah-langkah:

### 1. Buka Halaman Settings
1. Login ke aplikasi FlowSpec AI
2. Klik menu **Settings** di navigation bar
3. Atau kunjungi: `http://localhost:8000/settings`

### 2. Setup WhatsApp Notifications

#### A. Masukkan Nomor HP
1. Scroll ke bagian **"WhatsApp Notifications"**
2. Masukkan nomor WhatsApp Anda di field **"Phone Number"**
   - Format: `081234567890` (tanpa +62)
   - Atau: `6281234567890` (dengan country code)
   - Sistem akan otomatis format ke: `6281234567890`

#### B. Aktifkan Notifikasi
1. Centang checkbox **"Enable WhatsApp Notifications"**
2. Klik tombol **"Save Notification Settings"**
3. Anda akan menerima konfirmasi sukses

#### C. Menonaktifkan Notifikasi
1. Uncheck checkbox **"Enable WhatsApp Notifications"**
2. Klik tombol **"Save Notification Settings"**
3. WhatsApp notifikasi akan dinonaktifkan

### 3. Status Notifikasi

Setelah aktif, Anda akan melihat:
```
ℹ️ WhatsApp Notifications Active
You will receive reminders at: 6281234567890
```

### 4. Kapan Notifikasi Dikirim?

WhatsApp akan dikirim otomatis untuk:
- ✅ **Task** dengan deadline dalam **30 menit**
- ✅ **Meeting** yang akan dimulai dalam **30 menit**

### 5. Format Notifikasi

**Task Reminder:**
```
🔔 *Task Reminder - FlowSpec AI*

📋 *Task:* Prepare presentation
⏰ *Due:* 21 Feb 2026, 14:30
⚠️ *Time Left:* 25 minutes
🔴 *Priority:* High

Please complete this task soon! 💪
```

**Meeting Reminder:**
```
🔔 *Meeting Reminder - FlowSpec AI*

📅 *Meeting:* Team Standup
⏰ *Start:* 21 Feb 2026, 09:00
⚠️ *Starts in:* 15 minutes
🔗 *Link:* https://meet.google.com/abc-defg-hij

Don't be late! 🏃‍♂️
```

## Tips:

1. **Pastikan nomor HP benar** - Notifikasi tidak akan terkirim jika nomor salah
2. **Gunakan nomor WhatsApp aktif** - Pastikan nomor terdaftar di WhatsApp
3. **Scheduler harus running** - Jalankan `php artisan schedule:work` agar reminder otomatis
4. **Check status** - Lihat info box di halaman settings untuk konfirmasi status

## Troubleshooting:

### Notifikasi tidak terkirim?

1. **Cek nomor HP di Settings**
   - Pastikan nomor sudah benar
   - Pastikan checkbox aktif (centang)

2. **Cek Scheduler**
   ```bash
   # Jalankan scheduler
   php artisan schedule:work
   
   # Atau test manual
   php artisan reminders:whatsapp
   ```

3. **Cek Log**
   ```bash
   tail -f storage/logs/laravel.log | grep -i "whatsapp"
   ```

4. **Test Token Fonnte**
   - Login ke dashboard Fonnte
   - Cek quota dan status device

## Fitur Tambahan di Settings:

### ✅ Profile Information
- Update nama Anda
- Email (tidak bisa diubah)

### ✅ Email Notifications
- Otomatis aktif untuk semua user
- Notifikasi task & meeting via email

### ✅ Google Account
- Connect/Disconnect Google Account
- Untuk integrasi Calendar & Gmail

## Next: Test Notifikasi

1. **Set nomor HP** di Settings
2. **Aktifkan** checkbox WhatsApp notifications
3. **Buat task** dengan deadline dekat:
   ```
   Via AI Chat: "Buatkan task test reminder 25 menit lagi"
   ```
4. **Jalankan scheduler**:
   ```bash
   php artisan schedule:work
   ```
5. **Tunggu** - WhatsApp akan terkirim otomatis dalam 5 menit!

---

**Selamat! Fitur notifikasi WhatsApp sudah siap digunakan!** 🎉
