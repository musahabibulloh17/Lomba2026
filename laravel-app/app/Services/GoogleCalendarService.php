<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes([
            Calendar::CALENDAR,
            Calendar::CALENDAR_EVENTS,
        ]);
        $this->client->setAuthConfig([
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uris' => [config('services.google.redirect')],
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setUserToken(User $user)
    {
        if (!$user->google_access_token) {
            Log::warning('User does not have Google token', ['user_id' => $user->id]);
            return false;
        }

        // Decode token if it's stored as JSON string
        $accessToken = $user->google_access_token;
        if (is_string($accessToken)) {
            $accessToken = json_decode($accessToken, true);
        }

        $this->client->setAccessToken($accessToken);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $this->client->getAccessToken();
                
                $user->update([
                    'google_access_token' => json_encode($newToken)
                ]);
            } else {
                Log::warning('User Google token expired and no refresh token', ['user_id' => $user->id]);
                return false;
            }
        }

        $this->service = new Calendar($this->client);
        return true;
    }

    public function createEvent($title, $description, $startTime, $endTime, $attendees = [], $createMeetLink = false)
    {
        if (!$this->service) {
            Log::error('Google Calendar service not initialized');
            throw new \Exception('Google Calendar service not initialized. Please set user token first.');
        }

        Log::info('Creating Google Calendar event', [
            'title' => $title,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'attendees_count' => count($attendees),
            'create_meet_link' => $createMeetLink
        ]);

        $eventData = [
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => Carbon::parse($startTime)->toRfc3339String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => Carbon::parse($endTime)->toRfc3339String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'attendees' => array_map(function($email) {
                return ['email' => $email];
            }, $attendees),
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60], // 1 day before
                    ['method' => 'popup', 'minutes' => 30],
                ],
            ],
        ];

        // Add Google Meet conference if requested
        if ($createMeetLink) {
            $eventData['conferenceData'] = [
                'createRequest' => [
                    'requestId' => uniqid('meet-', true),
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ],
                ]
            ];
        }

        $event = new Event($eventData);

        try {
            Log::info('Attempting to insert event to Google Calendar', [
                'calendar' => 'primary',
                'has_conference' => $createMeetLink
            ]);
            
            // Use conferenceDataVersion parameter to enable Meet link creation
            $optParams = $createMeetLink ? ['conferenceDataVersion' => 1] : [];
            $createdEvent = $this->service->events->insert('primary', $event, $optParams);
            
            $meetLink = null;
            if ($createMeetLink && isset($createdEvent->conferenceData->entryPoints)) {
                foreach ($createdEvent->conferenceData->entryPoints as $entryPoint) {
                    if ($entryPoint->entryPointType === 'video') {
                        $meetLink = $entryPoint->uri;
                        break;
                    }
                }
            }
            
            Log::info('Google Calendar event created successfully', [
                'event_id' => $createdEvent->id,
                'title' => $title,
                'html_link' => $createdEvent->htmlLink,
                'meet_link' => $meetLink
            ]);
            
            return $createdEvent;
        } catch (\Exception $e) {
            Log::info('Attempting to insert event to Google Calendar', [
                'calendar' => 'primary'
            ]);
            
            $createdEvent = $this->service->events->insert('primary', $event);
            
            Log::info('Google Calendar event created successfully', [
                'event_id' => $createdEvent->id,
                'title' => $title,
                'html_link' => $createdEvent->htmlLink
            ]);
            
            return $createdEvent;
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event', [
                'error' => $e->getMessage(),
                'title' => $title,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    public function updateEvent($eventId, $title, $description, $startTime, $endTime, $attendees = [])
    {
        if (!$this->service) {
            throw new \Exception('Google Calendar service not initialized. Please set user token first.');
        }

        try {
            $event = $this->service->events->get('primary', $eventId);
            
            $event->setSummary($title);
            $event->setDescription($description);
            $event->setStart(new \Google\Service\Calendar\EventDateTime([
                'dateTime' => Carbon::parse($startTime)->toRfc3339String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ]));
            $event->setEnd(new \Google\Service\Calendar\EventDateTime([
                'dateTime' => Carbon::parse($endTime)->toRfc3339String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ]));
            
            if (!empty($attendees)) {
                $event->setAttendees(array_map(function($email) {
                    return new \Google\Service\Calendar\EventAttendee(['email' => $email]);
                }, $attendees));
            }

            $updatedEvent = $this->service->events->update('primary', $eventId, $event);
            
            Log::info('Google Calendar event updated', [
                'event_id' => $eventId,
                'title' => $title
            ]);
            
            return $updatedEvent;
        } catch (\Exception $e) {
            Log::error('Failed to update Google Calendar event', [
                'error' => $e->getMessage(),
                'event_id' => $eventId
            ]);
            throw $e;
        }
    }

    public function deleteEvent($eventId)
    {
        if (!$this->service) {
            throw new \Exception('Google Calendar service not initialized. Please set user token first.');
        }

        try {
            $this->service->events->delete('primary', $eventId);
            
            Log::info('Google Calendar event deleted', ['event_id' => $eventId]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete Google Calendar event', [
                'error' => $e->getMessage(),
                'event_id' => $eventId
            ]);
            return false;
        }
    }

    public function getEvents($maxResults = 10)
    {
        if (!$this->service) {
            throw new \Exception('Google Calendar service not initialized. Please set user token first.');
        }

        try {
            $optParams = [
                'maxResults' => $maxResults,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => Carbon::now()->toRfc3339String(),
            ];

            $results = $this->service->events->listEvents('primary', $optParams);
            return $results->getItems();
        } catch (\Exception $e) {
            Log::error('Failed to get Google Calendar events', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
