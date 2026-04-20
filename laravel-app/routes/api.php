<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\NLPController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
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
