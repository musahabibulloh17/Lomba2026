# Migration Guide: Node.js + React → Full Laravel (Backend + Blade Frontend)

## 📋 Status Migrasi

### ✅ Selesai

#### 1. Setup Laravel Project ✓
- Laravel 12 telah diinstall di folder `laravel-app/`
- PHP 8.4.2 & Composer 2.8.5 sudah siap
- Struktur project Laravel lengkap

#### 2. Database Models & Migrations ✓
**PostgreSQL Configuration:**
- Database: `workflow_ai_db`
- Driver: `pgsql` (perlu aktifkan extension di php.ini)
- File `.env` sudah dikonfigurasi

**Tables yang dibuat:**
- ✅ `users` - Extended dengan fields: role, timezone, preferences, google tokens, slack_user_id, is_active, last_login
- ✅ `tasks` - Priority, status, reminders, tags, assignments
- ✅ `meetings` - Google Calendar integration, attendees, meeting types
- ✅ `email_logs` - Email tracking dan history
- ✅ `nlp_commands` - NLP command history dengan OpenSpec workflow_spec
- ✅ `password_reset_tokens`
- ✅ `sessions`
- ✅ Cache & Jobs tables (dari Laravel default)

**Eloquent Models:**
- ✅ `User` - Dengan HasUuids, relationships, `has_google_auth` accessor
- ✅ `Task` - Dengan scopes (pending, completed, upcomingReminders)
- ✅ `Meeting` - Dengan scopes (scheduled, upcoming, needingReminder)
- ✅ `EmailLog` - Dengan scopes (pending, sent, failed)
- ✅ `NLPCommand` - Dengan scopes (successful, failed, byIntent)

#### 3. API Controllers ✓
Controllers yang dibuat di `app/Http/Controllers/Api/`:
- ✅ `TaskController` - Full CRUD + upcomingReminders
- ✅ `MeetingController` - CRUD operations
- ✅ `EmailController` - Email management
- ✅ `NLPController` - Natural language processing
- ✅ `AuthController` - Register, Login, Logout, Profile management

#### 4. Web Controllers ✓
Controllers untuk Blade views di `app/Http/Controllers/Web/`:
- ✅ `DashboardController` - Dashboard overview
- ✅ `TaskController` - Resource controller untuk task views
- ✅ `MeetingController` - Resource controller untuk meeting views
- ✅ `ChatController` - Chat interface dengan AI

### 🔄 Perlu Dikerjakan

#### 5. Setup PostgreSQL Extension
**PENTING - Harus dilakukan sebelum migration:**

1. Buka `C:\laragon\bin\php\php-8.4.2-nts-Win32-vs17-x64\php.ini`
2. Uncomment (hapus `;` di depan):
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```
3. Restart Laragon/Apache
4. Buat database:
   ```sql
   CREATE DATABASE workflow_ai_db;
   ```
5. Jalankan migrations:
   ```bash
   cd laravel-app
   php artisan migrate:fresh
   ```

#### 6. Install Laravel Sanctum
```bash
cd laravel-app
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Update `app/Http/Kernel.php` untuk menambahkan Sanctum middleware.

#### 7. Setup Services & Integrations

**Packages yang perlu diinstall:**
```bash
# Google APIs
composer require google/apiclient

# Slack Integration  
composer require slack/slack

# Gemini AI (PHP SDK atau HTTP Client)
composer require guzzlehttp/guzzle

# Mail
# Laravel sudah include mail support, tinggal konfigurasi
```

**Services yang perlu dibuat:**
- `app/Services/GoogleCalendarService.php` - Google Calendar integration
- `app/Services/GmailService.php` - Gmail integration
- `app/Services/SlackService.php` - Slack notifications
- `app/Services/NLPService.php` - Gemini AI integration
- `app/Services/WorkflowSpecGenerator.php` - OpenSpec workflow generator

#### 8. Setup API Routes

File: `routes/api.php`
```php
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\NLPController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::get('tasks/reminders/upcoming', [TaskController::class, 'upcomingReminders']);
    
    // Meetings
    Route::apiResource('meetings', MeetingController::class);
    
    // Emails
    Route::apiResource('emails', EmailController::class);
    
    // NLP
    Route::post('nlp/process', [NLPController::class, 'process']);
    Route::get('nlp/history', [NLPController::class, 'history']);
});
```

#### 9. Setup Web Routes & Blade Views

**Routes:** `routes/web.php`
```php
<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\MeetingController;
use App\Http\Controllers\Web\ChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('tasks', TaskController::class);
    Route::resource('meetings', MeetingController::class);
    
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');
    
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
});

require __DIR__.'/auth.php';
```

**Blade Views yang perlu dibuat:**
```
resources/views/
├── layouts/
│   ├── app.blade.php          # Main layout
│   ├── navigation.blade.php   # Navigation bar
│   └── guest.blade.php        # Guest layout
├── dashboard/
│   ├── index.blade.php        # Dashboard
│   └── settings.blade.php     # Settings page
├── tasks/
│   ├── index.blade.php        # Task list
│   ├── create.blade.php       # Create task
│   ├── edit.blade.php         # Edit task
│   └── show.blade.php         # Show task
├── meetings/
│   ├── index.blade.php        # Meeting list
│   ├── create.blade.php       # Create meeting
│   ├── edit.blade.php         # Edit meeting
│   └── show.blade.php         # Show meeting
├── chat/
│   └── index.blade.php        # Chat interface
└── auth/
    ├── login.blade.php
    └── register.blade.php
```

#### 10. Frontend (Blade + Alpine.js)

**Install Alpine.js & Tailwind CSS:**
```bash
npm install -D tailwindcss postcss autoprefixer alpinejs
npx tailwindcss init -p
```

**Update `tailwind.config.js`:**
```js
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

**Add to `resources/css/app.css`:**
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

**Add to `resources/js/app.js`:**
```js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

**Build assets:**
```bash
npm install
npm run build
```

#### 11. Environment Configuration

File: `.env`
```env
APP_NAME="FlowSpec AI"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=workflow_ai_db
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Google APIs
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback

# Gemini AI
GEMINI_API_KEY=your-gemini-api-key

# Slack
SLACK_BOT_TOKEN=your-slack-bot-token
SLACK_SIGNING_SECRET=your-signing-secret

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### 12. Docker Configuration

**Create:** `laravel-app/Dockerfile`
```dockerfile
FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD ["php-fpm"]
```

**Update:** `docker-compose.yml`
```yaml
version: '3.8'

services:
  app:
    build:
      context: ./laravel-app
      dockerfile: Dockerfile
    container_name: flowspec-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./laravel-app:/var/www
    networks:
      - flowspec-network

  nginx:
    image: nginx:alpine
    container_name: flowspec-nginx
    restart: unless-stopped
    ports:
      - "80:80"
    volumes:
      - ./laravel-app:/var/www
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    networks:
      - flowspec-network

  postgres:
    image: postgres:14
    container_name: flowspec-db
    restart: unless-stopped
    environment:
      POSTGRES_DB: workflow_ai_db
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: postgres
    ports:
      - "5432:5432"
    volumes:
      - postgres-data:/var/lib/postgresql/data
    networks:
      - flowspec-network

networks:
  flowspec-network:
    driver: bridge

volumes:
  postgres-data:
```

## 📝 Perbandingan Struktur

### Sebelum (Node.js + React)
```
Lomba2026/
├── backend/          # Node.js + Express
│   └── src/
│       ├── controllers/
│       ├── models/   # Sequelize
│       ├── routes/
│       └── services/
└── frontend/         # React SPA
    └── src/
        ├── components/
        ├── pages/
        └── services/
```

### Sesudah (Full Laravel)
```
Lomba2026/
└── laravel-app/      # Full Laravel
    ├── app/
    │   ├── Http/
    │   │   └── Controllers/
    │   │       ├── Api/      # API Controllers
    │   │       └── Web/      # Web Controllers
    │   ├── Models/           # Eloquent Models
    │   └── Services/         # Business Logic
    ├── database/
    │   └── migrations/       # Database migrations
    ├── resources/
    │   ├── views/            # Blade Templates
    │   ├── css/              # Tailwind CSS
    │   └── js/               # Alpine.js
    └── routes/
        ├── api.php           # API Routes
        └── web.php           # Web Routes
```

## 🚀 Next Steps

1. **Aktifkan PostgreSQL extension di PHP** (PENTING!)
2. **Jalankan migrations:** `php artisan migrate:fresh`
3. **Install Sanctum:** `composer require laravel/sanctum`
4. **Install dependencies:** Packages untuk Google, Slack, Gemini
5. **Buat Services:** Migrate logic dari Node.js services
6. **Setup Routes:** API dan Web routes
7. **Buat Blade Views:** Convert React components ke Blade + Alpine.js
8. **Test integrations:** Google Calendar, Gmail, Slack, Gemini AI
9. **Update Docker:** Test containerization
10. **Deploy:** Update K8s configs

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [PostgreSQL Laravel Driver](https://laravel.com/docs/database#postgresql)

---

**Created:** 2026-02-21
**Laravel Version:** 12.52.0
**PHP Version:** 8.4.2
