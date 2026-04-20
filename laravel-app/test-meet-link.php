<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\GoogleCalendarService;

// Get the logged-in user
$user = User::where('email', 'musahabibullah3@gmail.com')->first();

if (!$user) {
    die("User not found\n");
}

$calendarService = new GoogleCalendarService();

if (!$calendarService->setUserToken($user)) {
    die("Failed to set user token\n");
}

// Test creating event with Meet link
$title = "TEST: Google Meet Link Generation";
$description = "Testing automatic Google Meet link creation via API";
$startTime = now()->addDay()->setTime(14, 0); // Tomorrow at 2 PM
$endTime = now()->addDay()->setTime(15, 0);   // Tomorrow at 3 PM

try {
    $event = $calendarService->createEvent(
        $title,
        $description,
        $startTime,
        $endTime,
        [],
        true // Enable Google Meet link creation
    );
    
    echo "Event created successfully!\n";
    echo "Event ID: " . $event->id . "\n";
    echo "Event Link: " . $event->htmlLink . "\n\n";
    
    // Check for Google Meet link
    if (isset($event->conferenceData)) {
        echo "Conference Data Found!\n";
        if (isset($event->conferenceData->entryPoints)) {
            foreach ($event->conferenceData->entryPoints as $entryPoint) {
                if ($entryPoint->entryPointType === 'video') {
                    echo "✓ Google Meet Link: " . $entryPoint->uri . "\n";
                    break;
                }
            }
        } else {
            echo "✗ No entry points found\n";
        }
    } else {
        echo "✗ No conference data found\n";
        echo "This might mean:\n";
        echo "1. The conferenceDataVersion parameter wasn't set\n";
        echo "2. The API didn't process the conference request\n";
        echo "3. Google Meet isn't enabled for this workspace\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
