# Gemini Chatbot - Quick Test Guide

## ✅ Chatbot Sudah Aktif!

Masalah yang diperbaiki:
- ✅ Type hint `int $userId` → `string $userId` (UUID support)
- ✅ GeminiService dengan retry logic & rate limiting
- ✅ Intent detection untuk task & meeting management
- ✅ Action execution untuk semua commands

## 🧪 Test Chatbot

### 1. Buka Browser
```
http://127.0.0.1:8000/login
Email: test@example.com
Password: password
```

### 2. Navigasi ke Chat
Klik **"Chat with AI"** di sidebar atau:
```
http://127.0.0.1:8000/chat
```

### 3. Test Commands

#### Test 1: Create Task
```
Ketik: "Create a task to prepare presentation for Monday with high priority"

Expected Response:
✅ Task created: "prepare presentation for Monday" (Priority: high)
```

#### Test 2: List Tasks
```
Ketik: "Show me all my pending tasks"

Expected Response:
Here are your tasks:
• [List of your tasks with status and priority]
```

#### Test 3: Schedule Meeting
```
Ketik: "Schedule team meeting tomorrow at 2pm for project review"

Expected Response:
📅 Meeting scheduled: "team meeting" at Feb 22, 2024 2:00 PM
```

#### Test 4: List Meetings
```
Ketik: "Show my upcoming meetings"

Expected Response:
Here are your upcoming meetings:
• [List of upcoming meetings with time]
```

#### Test 5: General Chat
```
Ketik: "How many tasks do I have?"

Expected Response:
[AI will respond based on your actual task count with context]
```

## 🔧 Manual API Test (Optional)

Test langsung dengan command:
```bash
php test-gemini.php
```

Expected Output:
```
Testing Gemini API...

1. Testing simple generation...
Response: Hello

2. Testing NLP command processing...
Intent: create_task
Confidence: 0.98
Response: I've noted down 'test the API' as a new task...
```

## 📊 Check Database

Setelah test, cek database untuk melihat data tersimpan:

```bash
php artisan tinker
```

```php
// Check NLP commands
\App\Models\NLPCommand::latest()->take(5)->get();

// Check tasks created via chat
\App\Models\Task::where('user_id', auth()->id())->latest()->get();

// Check meetings created via chat
\App\Models\Meeting::where('user_id', auth()->id())->latest()->get();
```

## 🎯 Features Working

✅ **Gemini AI Integration**
- Model: gemini-2.0-flash-exp
- Temperature: 0.3
- Max tokens: 1000
- Retry logic: 3 attempts with exponential backoff

✅ **Intent Detection**
- create_task
- list_tasks
- create_meeting
- list_meetings
- general_query

✅ **Action Execution**
- Task creation with title, description, priority, due_date
- Task listing with filters (status, priority)
- Meeting creation with title, time, type
- Meeting listing (upcoming by default)
- General conversational responses

✅ **Data Storage**
- All commands stored in `nlp_commands` table
- Includes: intent, entities, response, processing_time, workflow_spec
- Success/error tracking

## 🐛 Jika Masih Error

1. **Check Logs**
```bash
Get-Content storage\logs\laravel.log -Tail 30
```

2. **Clear Cache**
```bash
php artisan optimize:clear
```

3. **Restart Server**
```bash
# Stop current server (Ctrl+C)
php artisan serve
```

4. **Check Console Browser**
- Buka Developer Tools (F12)
- Tab Console untuk JavaScript errors
- Tab Network untuk API responses

## 🎉 Success Indicators

✅ AI Status: "Gemini AI Active" (green dot)
✅ Messages sent successfully
✅ AI responds in chat
✅ Tasks/meetings created in database
✅ No errors in browser console
✅ No errors in Laravel log

Selamat! Chatbot Gemini AI sekarang fully functional! 🚀
