# Slack Integration Setup Guide

## 📋 Overview
Aplikasi ini mendukung integrasi Slack untuk mengirim notifikasi otomatis tentang:
- Task baru yang dibuat
- Meeting yang dijadwalkan
- Reminder untuk task dan meeting yang akan datang
- Daily summary tasks

## 🔧 Setup Backend (Slack Bot)

### 1. Buat Slack App
1. Buka https://api.slack.com/apps
2. Klik **"Create New App"** → **"From scratch"**
3. Masukkan:
   - **App Name**: "AI Workflow Assistant" (atau nama lain)
   - **Workspace**: Pilih workspace Slack Anda
4. Klik **"Create App"**

### 2. Setup Bot Token
1. Di menu sidebar, klik **"OAuth & Permissions"**
2. Scroll ke **"Scopes"** → **"Bot Token Scopes"**
3. Tambahkan permission berikut:
   - `chat:write` - Kirim pesan ke channel
   - `chat:write.public` - Kirim pesan ke public channel tanpa join
   - `users:read` - Baca info user
   - `channels:read` - Baca list channel
   - `im:write` - Kirim direct message ke user

### 3. Install App ke Workspace
1. Scroll ke atas halaman **"OAuth & Permissions"**
2. Klik **"Install to Workspace"**
3. Review permissions dan klik **"Allow"**
4. Copy **"Bot User OAuth Token"** (mulai dengan `xoxb-...`)

### 4. Setup Environment Variables
Tambahkan ke file `.env` di folder backend:

```env
# Slack Configuration
SLACK_BOT_TOKEN=xoxb-your-bot-token-here
SLACK_DEFAULT_CHANNEL=#general
```

### 5. Restart Backend Server
```bash
cd backend
npm run dev
```

## 👤 Setup User (Connect Slack Account)

### Cara Menemukan Slack User ID

#### Method 1: Dari Slack Desktop/Web
1. Buka Slack
2. Klik pada profile picture Anda di pojok kanan atas
3. Klik **"Profile"**
4. Klik tombol three dots (•••) → **"Copy member ID"**
5. User ID akan tercopy ke clipboard (format: `U01234ABCDE`)

#### Method 2: Dari URL Profile
1. Buka Slack
2. Klik pada nama Anda untuk buka profile
3. Lihat URL di browser: `https://your-workspace.slack.com/team/U01234ABCDE`
4. User ID adalah bagian setelah `/team/` (contoh: `U01234ABCDE`)

#### Method 3: Via Slack API (untuk admin)
```bash
curl -X GET "https://slack.com/api/users.list" \
  -H "Authorization: Bearer xoxb-your-bot-token"
```

### Menghubungkan Slack di Aplikasi
1. Login ke aplikasi
2. Buka **Settings** page
3. Scroll ke bagian **"Slack Integration"**
4. Paste **Slack User ID** Anda (contoh: `U01234ABCDE`)
5. Klik **"Connect Slack"**
6. Aktifkan **"Slack Notifications"** di Preferences

## 🧪 Testing Slack Integration

### Test Manual via API
```bash
# Test kirim pesan ke channel
curl -X POST http://localhost:5000/api/slack/send \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "channel": "#general",
    "message": "Test notification from AI Workflow Assistant!"
  }'
```

### Test via Chat Interface
1. Login ke aplikasi
2. Buka **Chat** page
3. Kirim pesan: "Kirim pesan ke Slack: Testing notifikasi"
4. Bot akan proses dan kirim ke Slack default channel

### Test Automatic Notifications
1. Buat task baru dengan due date
2. Aktifkan Slack notifications di Settings
3. Task notification akan otomatis terkirim ke Slack DM Anda

## 📤 Jenis Notifikasi

### 1. Task Created
```
🆕 New Task Created
📝 Task: [Task Title]
📅 Due: [Due Date]
⏰ Priority: [high/medium/low]
```

### 2. Meeting Scheduled
```
📅 Meeting Scheduled
📝 Title: [Meeting Title]
👥 Participants: [Emails]
🕐 Time: [Start Time] - [End Time]
```

### 3. Task Reminder (15 menit sebelum due)
```
⏰ Task Reminder
📝 Task: [Task Title]
📅 Due in 15 minutes!
Status: [pending/in-progress]
```

### 4. Meeting Reminder (30 menit sebelum meeting)
```
⏰ Meeting Reminder
📝 Meeting: [Title]
🕐 Starts in 30 minutes
📍 Location: [Meeting Location]
```

### 5. Daily Summary (setiap hari jam 9 pagi)
```
📊 Daily Task Summary
✅ Completed: X tasks
⏳ Pending: Y tasks
🔴 Overdue: Z tasks
```

## ⚙️ Konfigurasi Notifikasi

Di **Settings** → **Preferences**:
- ☑️ **Email Notifications** - Aktifkan email notifications
- ☑️ **Slack Notifications** - Aktifkan Slack notifications
- ⏰ **Reminder Minutes** - Waktu reminder sebelum due (default: 15 menit)

## 🔍 Troubleshooting

### Slack notifications tidak terkirim
1. ✅ Pastikan `SLACK_BOT_TOKEN` sudah diset di `.env`
2. ✅ Pastikan bot sudah installed di workspace
3. ✅ Pastikan Slack User ID benar (mulai dengan `U`)
4. ✅ Pastikan Slack Notifications aktif di preferences
5. ✅ Check backend logs untuk error

### Bot tidak bisa kirim ke channel
1. Invite bot ke channel: ketik `/invite @YourBotName` di channel
2. Atau tambahkan scope `chat:write.public` agar bisa kirim tanpa join

### User ID tidak valid
- User ID harus format: `U` + 10-11 karakter alphanumeric
- Contoh valid: `U01234ABCDE`, `U987ZXYWVUT`
- Contoh INVALID: `@username`, `username`, `user@email.com`

## 📚 Resources
- [Slack API Documentation](https://api.slack.com/docs)
- [Bot Token Scopes](https://api.slack.com/scopes)
- [Message Formatting](https://api.slack.com/reference/surfaces/formatting)
- [Block Kit Builder](https://api.slack.com/block-kit/building)

## 🔐 Security Notes
- ⚠️ Jangan commit `SLACK_BOT_TOKEN` ke repository
- ✅ Simpan token di `.env` file (sudah ada di .gitignore)
- ✅ Gunakan environment variables untuk production
- ✅ Rotate token secara berkala di Slack App settings
