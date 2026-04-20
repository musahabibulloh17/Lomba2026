<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;

$task = Task::where('title', 'Test WhatsApp Reminder')->first();

if ($task) {
    $user = $task->user;
    $user->phone_number = '6285748077009';
    $user->whatsapp_notifications = true;
    $user->save();
    
    echo "✅ Updated user: {$user->name}\n";
    echo "📱 Phone: {$user->phone_number}\n";
    echo "🔔 WA Enabled: " . ($user->whatsapp_notifications ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Task 'Test WhatsApp Reminder' not found\n";
}
