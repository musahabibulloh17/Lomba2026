# 📚 Contoh Penggunaan

## Quick Start

### 1. Login

Pertama, login untuk mendapatkan JWT token:

```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

Response:
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "user-id",
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

Simpan token untuk request selanjutnya!

---

## Contoh Perintah Natural Language

### 1. Jadwalkan Meeting

**Perintah:**
```
"Jadwalkan pertemuan besok jam 10 pagi dengan Tim Marketing untuk diskusi campaign Q1"
```

**Request:**
```bash
curl -X POST http://localhost:5000/api/nlp/process \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "command": "Jadwalkan pertemuan besok jam 10 pagi dengan Tim Marketing untuk diskusi campaign Q1"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Meeting berhasil dijadwalkan untuk besok pukul 10:00 dengan Tim Marketing",
  "intent": "schedule_meeting",
  "entities": {
    "title": "Pertemuan dengan Tim Marketing",
    "description": "Diskusi campaign Q1",
    "date": "2026-02-06",
    "time": "10:00",
    "attendees": ["Tim Marketing"]
  },
  "result": {
    "id": "meeting-id",
    "title": "Pertemuan dengan Tim Marketing",
    "googleEventId": "google-event-123",
    "meetingLink": "https://meet.google.com/xxx-yyyy-zzz"
  }
}
```

### 2. Buat Task Baru

**Perintah:**
```
"Buat task baru review dokumen proposal dengan prioritas tinggi deadline 3 hari lagi"
```

**Response:**
```json
{
  "success": true,
  "message": "Task 'Review dokumen proposal' berhasil dibuat dengan prioritas tinggi",
  "intent": "create_task",
  "entities": {
    "title": "Review dokumen proposal",
    "priority": "high",
    "date": "2026-02-08"
  },
  "result": {
    "id": "task-id",
    "title": "Review dokumen proposal",
    "priority": "high",
    "status": "pending",
    "dueDate": "2026-02-08T23:59:59.000Z"
  }
}
```

### 3. Kirim Email Pengingat

**Perintah:**
```
"Kirim email ke team@example.com dengan subject 'Pengingat Meeting' dan isi 'Jangan lupa meeting besok jam 10'"
```

**Response:**
```json
{
  "success": true,
  "message": "Email berhasil dikirim ke team@example.com",
  "intent": "send_email",
  "entities": {
    "email_to": "team@example.com",
    "subject": "Pengingat Meeting",
    "message": "Jangan lupa meeting besok jam 10"
  },
  "result": {
    "messageId": "gmail-message-id",
    "status": "sent"
  }
}
```

### 4. Kirim Notifikasi Slack

**Perintah:**
```
"Kirim pesan ke channel #general: Reminder meeting team dalam 15 menit"
```

**Response:**
```json
{
  "success": true,
  "message": "Pesan berhasil dikirim ke Slack channel #general",
  "intent": "send_slack_message",
  "entities": {
    "slack_channel": "#general",
    "message": "Reminder meeting team dalam 15 menit"
  },
  "result": {
    "timestamp": "1234567890.123456"
  }
}
```

### 5. Update Task

**Perintah:**
```
"Update task dengan id [task-id] status jadi completed"
```

### 6. Lihat Jadwal

**Perintah:**
```
"Tampilkan jadwal meeting minggu ini"
```

**Response:**
```json
{
  "success": true,
  "message": "Ditemukan 3 meeting minggu ini",
  "intent": "get_calendar",
  "result": [
    {
      "id": "meeting-1",
      "title": "Team Standup",
      "startTime": "2026-02-06T09:00:00.000Z"
    },
    {
      "id": "meeting-2",
      "title": "Project Review",
      "startTime": "2026-02-08T14:00:00.000Z"
    }
  ]
}
```

---

## Manual API Usage

### Membuat Task Manual

```bash
curl -X POST http://localhost:5000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Review Code",
    "description": "Review PR #123",
    "priority": "high",
    "status": "pending",
    "dueDate": "2026-02-10T17:00:00.000Z",
    "tags": ["code-review", "urgent"]
  }'
```

### Update Task Status

```bash
curl -X PUT http://localhost:5000/api/tasks/task-id \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed",
    "completedAt": "2026-02-05T15:30:00.000Z"
  }'
```

### Jadwalkan Meeting Manual

```bash
curl -X POST http://localhost:5000/api/meetings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Sprint Planning",
    "description": "Plan sprint untuk Q1 2026",
    "startTime": "2026-02-10T10:00:00.000Z",
    "endTime": "2026-02-10T12:00:00.000Z",
    "meetingType": "online",
    "attendees": [
      {"email": "john@example.com"},
      {"email": "jane@example.com"}
    ],
    "reminderMinutes": 30
  }'
```

### Kirim Email Manual

```bash
curl -X POST http://localhost:5000/api/email/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "recipient@example.com",
    "subject": "Task Reminder",
    "body": "<h2>Reminder</h2><p>Jangan lupa complete task hari ini</p>",
    "type": "reminder"
  }'
```

---

## Frontend Usage

### Login Page

1. Buka `http://localhost:3000/login`
2. Masukkan credentials:
   - Email: `john@example.com`
   - Password: `password123`
3. Klik "Login"

### Chat dengan AI

1. Navigasi ke "Chat AI" di sidebar
2. Ketik perintah natural language:
   - "Jadwalkan meeting besok jam 10"
   - "Buat task baru dengan prioritas tinggi"
   - "Tampilkan jadwal minggu ini"
3. AI akan memproses dan mengeksekusi perintah

### Task Management

1. Navigasi ke "Tasks"
2. Klik "Buat Task Baru"
3. Isi form dan simpan
4. Edit atau hapus task dengan icon di tabel

### Meeting Management

1. Navigasi ke "Meetings"
2. Klik "Jadwalkan Meeting"
3. Isi detail meeting
4. Meeting otomatis disync ke Google Calendar (jika terhubung)

---

## Integration Setup

### Google Calendar & Gmail

1. Buka Settings
2. Klik "Connect Google Account"
3. Authorize akses di popup window
4. Token akan tersimpan otomatis

### Slack Integration

1. Buat Slack App di https://api.slack.com/apps
2. Enable Bot Token Scopes:
   - `chat:write`
   - `channels:read`
   - `users:read`
3. Install app ke workspace
4. Copy Bot Token ke `.env`:
   ```
   SLACK_BOT_TOKEN=xoxb-your-token
   ```
5. Update Slack User ID di Settings

---

## Automation Examples

### Cron Job - Automatic Reminders

Sistem otomatis mengirim reminder untuk:

1. **Task Reminders** - 15 menit sebelum deadline (default)
2. **Meeting Reminders** - Sebelum meeting dimulai
3. **Overdue Tasks** - Daily check untuk task yang terlambat

Notifikasi dikirim via:
- 📧 Email (Gmail)
- 💬 Slack

### Webhook Integration (Future)

Coming soon: Real-time webhooks untuk event:
- Task created/updated/completed
- Meeting scheduled/cancelled
- Email sent/failed
- Slack notification sent

---

## Best Practices

### Natural Language Commands

**✅ Good Commands:**
```
- "Jadwalkan meeting besok jam 10 pagi dengan Tim Marketing"
- "Buat task review dokumen dengan prioritas tinggi deadline 3 hari lagi"
- "Kirim email ke john@example.com reminder untuk meeting besok"
```

**❌ Bad Commands:**
```
- "meeting" (terlalu singkat)
- "buat task" (kurang detail)
- "kirim email" (tidak ada recipient atau content)
```

### Tips

1. **Be Specific** - Semakin spesifik perintah, semakin baik hasil
2. **Include Time** - Selalu sertakan waktu untuk meeting/deadline
3. **Mention Recipients** - Sebutkan siapa yang terlibat
4. **Use Priority** - Tentukan prioritas untuk task (high/medium/low)

---

## Troubleshooting

### Token Expired

Jika mendapat error 401, login ulang untuk mendapat token baru.

### Google Calendar Not Syncing

1. Check apakah Google account sudah terhubung
2. Verify API credentials di `.env`
3. Check logs untuk error details

### Slack Notifications Not Working

1. Verify `SLACK_BOT_TOKEN` di `.env`
2. Check apakah bot sudah di-invite ke channel
3. Verify Slack User ID di profile settings

### NLP Command Not Working

1. Check apakah `OPENAI_API_KEY` valid
2. Coba perintah yang lebih spesifik
3. Check response untuk `suggestion` field

---

## Demo Video

*(Placeholder for demo video link)*

---

## Support

Jika mengalami masalah:
1. Check logs: `docker-compose logs -f backend`
2. Buka GitHub Issues
3. Contact support team
