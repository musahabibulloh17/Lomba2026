<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\GoogleCalendarService;
use Google\Service\Calendar;

$user = User::where('email', 'musahabibullah3@gmail.com')->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit;
}

echo "✅ User: {$user->name}\n\n";

$calendarService = new GoogleCalendarService();

if (!$calendarService->setUserToken($user)) {
    echo "❌ Failed to set token\n";
    exit;
}

echo "📅 Fetching recent events from Google Calendar...\n\n";

try {
    // Get events from yesterday to next week
    $optParams = [
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c', strtotime('-1 day')),
        'timeMax' => date('c', strtotime('+7 days')),
    ];
    
    $service = new Calendar($calendarService->getClient());
    $results = $service->events->listEvents('primary', $optParams);
    $events = $results->getItems();

    if (empty($events)) {
        echo "❌ No events found in the next 7 days\n";
    } else {
        echo "Found " . count($events) . " events:\n\n";
        foreach ($events as $event) {
            $start = $event->start->dateTime ?: $event->start->date;
            echo "📌 " . $event->getSummary() . "\n";
            echo "   Time: " . date('Y-m-d H:i', strtotime($start)) . "\n";
            echo "   ID: " . $event->getId() . "\n";
            echo "   Link: " . $event->getHtmlLink() . "\n\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
