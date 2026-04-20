<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\MeetingController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\SettingsController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Home - Dashboard (Protected)
Route::middleware(['auth'])->group(function () {
    // Redirect home to dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    
    // Meetings
    Route::resource('meetings', MeetingController::class);
    
    // Chat with AI
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');
    Route::delete('/chat/history', [ChatController::class, 'clearHistory'])->name('chat.clear');
    Route::delete('/chat/message/{id}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update.profile');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.update.notifications');
    Route::delete('/settings/google', [SettingsController::class, 'disconnectGoogle'])->name('settings.disconnect.google');
});

// Test route for debugging (remove in production)
Route::post('/test-chat-api', function(\Illuminate\Http\Request $request) {
    \Log::info('Test chat API called', ['data' => $request->all()]);
    
    return response()->json([
        'success' => true,
        'response' => 'This is a test response. Your message was: ' . $request->input('message'),
        'timestamp' => now()->toDateTimeString()
    ]);
});
