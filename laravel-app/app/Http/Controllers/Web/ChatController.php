<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NLPCommand;
use App\Models\Task;
use App\Models\Meeting;
use App\Services\GeminiService;
use App\Services\GoogleCalendarService;
use App\Services\GmailService;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatController extends Controller
{
    protected $gemini;
    protected $calendarService;
    protected $gmailService;

    public function __construct(
        GeminiService $gemini,
        GoogleCalendarService $calendarService,
        GmailService $gmailService
    ) {
        $this->gemini = $gemini;
        $this->calendarService = $calendarService;
        $this->gmailService = $gmailService;
    }

    public function index()
    {
        $recentCommands = NLPCommand::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        return view('chat.index', compact('recentCommands'));
    }
    
    public function send(Request $request)
    {
        // Increase timeout for Gemini API calls
        set_time_limit(120);
        
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $startTime = microtime(true);
        $userId = auth()->id();
        $message = $validated['message'];

        Log::info("Chat message received", ['user_id' => $userId, 'message' => $message]);

        try {
            // Process command with Gemini AI
            Log::info("Processing command with Gemini...");
            $nlpResult = $this->gemini->processCommand($message);
            Log::info("Gemini processing complete", ['intent' => $nlpResult['intent']]);
            
            $intent = $nlpResult['intent'];
            $entities = $nlpResult['entities'];
            $confidence = $nlpResult['confidence'];
            $naturalResponse = $nlpResult['natural_response'];
            
            // Execute action based on intent
            Log::info("Executing action", ['intent' => $intent]);
            $actionResult = $this->executeAction($intent, $entities, $userId);
            Log::info("Action executed", ['success' => $actionResult['success'] ?? false]);
            
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Store command with result
            $nlpCommand = NLPCommand::create([
                'user_id' => $userId,
                'command' => $message,
                'intent' => $intent,
                'entities' => json_encode($entities),
                'response' => $actionResult['response'] ?? $naturalResponse,
                'action_taken' => $actionResult['action'] ?? 'none',
                'success' => $actionResult['success'] ?? true,
                'error_message' => $actionResult['error'] ?? null,
                'processing_time' => $processingTime,
                'workflow_spec' => json_encode($actionResult['workflow_spec'] ?? []),
            ]);

            return response()->json([
                'success' => true,
                'response' => $nlpCommand->response,
                'command' => $nlpCommand,
                'intent' => $intent,
                'confidence' => $confidence,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Chat processing error: ' . $e->getMessage());
            
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $nlpCommand = NLPCommand::create([
                'user_id' => $userId,
                'command' => $message,
                'intent' => 'error',
                'entities' => json_encode([]),
                'response' => 'Sorry, I encountered an error: ' . $e->getMessage(),
                'action_taken' => 'none',
                'success' => false,
                'error_message' => $e->getMessage(),
                'processing_time' => $processingTime,
            ]);

            return response()->json([
                'success' => false,
                'response' => $nlpCommand->response,
                'command' => $nlpCommand,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Execute action based on detected intent
     */
    protected function executeAction(string $intent, array $entities, string $userId): array
    {
        switch ($intent) {
            case 'create_task':
                return $this->createTask($entities, $userId);
                
            case 'list_tasks':
                return $this->listTasks($entities, $userId);
                
            case 'update_task':
                return $this->updateTask($entities, $userId);
                
            case 'delete_task':
                return $this->deleteTask($entities, $userId);
                
            case 'create_meeting':
                return $this->createMeeting($entities, $userId);
                
            case 'list_meetings':
                return $this->listMeetings($entities, $userId);
                
            case 'update_meeting':
                return $this->updateMeeting($entities, $userId);
                
            case 'delete_meeting':
                return $this->deleteMeeting($entities, $userId);
                
            case 'general_query':
                // Get context for better responses
                $context = [
                    'total_tasks' => Task::where('user_id', $userId)->count(),
                    'pending_tasks' => Task::where('user_id', $userId)->where('status', 'pending')->count(),
                    'upcoming_meetings' => Meeting::where('user_id', $userId)
                        ->where('start_time', '>', now())
                        ->count(),
                ];
                
                $response = $this->gemini->chat($entities['message'] ?? 'Hello', $context);
                
                return [
                    'success' => true,
                    'action' => 'chat',
                    'response' => $response,
                ];
            
            case 'error':
                // Return error response from Gemini
                return [
                    'success' => false,
                    'action' => 'error',
                    'response' => $entities['message'] ?? 'An error occurred processing your request.',
                ];
                
            default:
                return [
                    'success' => true,
                    'action' => 'none',
                    'response' => "I'm not sure how to help with that. Try asking me to create a task or schedule a meeting!",
                ];
        }
    }

    protected function createTask(array $entities, string $userId): array
    {
        if (empty($entities['title'])) {
            return [
                'success' => false,
                'action' => 'create_task_failed',
                'response' => 'Please provide a title for the task.',
                'error' => 'Missing title',
            ];
        }

        $user = auth()->user();
        
        \Log::info('Creating task', [
            'user_id' => $userId,
            'user_email' => $user->email,
            'has_google_token' => !empty($user->google_access_token),
            'has_refresh_token' => !empty($user->google_refresh_token),
            'task_has_due_date' => !empty($entities['due_date'])
        ]);
        
        $task = Task::create([
            'user_id' => $userId,
            'title' => $entities['title'],
            'description' => $entities['description'] ?? null,
            'due_date' => $entities['due_date'] ?? null,
            'priority' => $entities['priority'] ?? 'medium',
            'status' => 'pending',
        ]);

        $response = "✅ Task created: \"{$task->title}\" (Priority: {$task->priority})";
        
        // If user has Google authentication and task has due date, create calendar event
        if ($user->google_access_token && $task->due_date) {
            \Log::info('Attempting Google Calendar sync', [
                'task_id' => $task->id,
                'due_date' => $task->due_date
            ]);
            
            try {
                if ($this->calendarService->setUserToken($user)) {
                    $dueDate = Carbon::parse($task->due_date);
                    $startTime = $dueDate->copy()->setTime(9, 0); // 9 AM
                    $endTime = $dueDate->copy()->setTime(10, 0); // 10 AM
                    
                    $calendarEvent = $this->calendarService->createEvent(
                        "Task: {$task->title}",
                        $task->description ?? "Task created via FlowSpec AI",
                        $startTime,
                        $endTime
                    );
                    
                    \Log::info('Calendar event created', [
                        'event_id' => $calendarEvent->id,
                        'task_id' => $task->id
                    ]);
                    
                    // Store Google event ID
                    $task->update(['google_event_id' => $calendarEvent->id]);
                    
                    $response .= "\n📅 Added to Google Calendar!";
                    
                    // Send email notification with beautiful HTML template
                    if ($this->gmailService->setUserToken($user)) {
                        $priorityColor = $task->priority === 'high' ? '#ef4444' : ($task->priority === 'medium' ? '#f59e0b' : '#10b981');
                        $priorityEmoji = $task->priority === 'high' ? '🔴' : ($task->priority === 'medium' ? '🟡' : '🟢');
                        
                        $emailBody = "
                        <html>
                        <head>
                            <style>
                                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
                                .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                                .header { background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); color: white; padding: 30px; text-align: center; }
                                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                                .header p { margin: 5px 0 0 0; opacity: 0.9; }
                                .content { padding: 30px; }
                                .task-card { background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 20px; border-radius: 8px; margin: 20px 0; }
                                .task-title { font-size: 22px; font-weight: 600; color: #1f2937; margin: 0 0 15px 0; }
                                .detail-row { display: flex; margin: 12px 0; align-items: center; }
                                .detail-icon { width: 24px; height: 24px; margin-right: 12px; font-size: 20px; }
                                .detail-label { font-weight: 600; color: #4b5563; margin-right: 8px; }
                                .detail-value { color: #1f2937; }
                                .priority-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; margin: 10px 0; }
                                .description { background: white; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; margin: 15px 0; color: #4b5563; }
                                .calendar-button { display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; text-align: center; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3); }
                                .footer { background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 13px; border-top: 1px solid #e5e7eb; }
                                .success-note { background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin: 15px 0; font-size: 14px; text-align: center; font-weight: 500; }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                    <h1>✅ New Task Created</h1>
                                    <p>A new task has been added to your workflow</p>
                                </div>
                                <div class='content'>
                                    <div class='task-card'>
                                        <h2 class='task-title'>{$task->title}</h2>
                                        
                                        <div class='detail-row'>
                                            <span class='detail-icon'>📅</span>
                                            <span class='detail-label'>Due Date:</span>
                                            <span class='detail-value'>{$dueDate->format('l, F j, Y')}</span>
                                        </div>
                                        
                                        <div class='detail-row'>
                                            <span class='detail-icon'>⏰</span>
                                            <span class='detail-label'>Time:</span>
                                            <span class='detail-value'>{$dueDate->format('g:i A')}</span>
                                        </div>
                                        
                                        <div class='detail-row'>
                                            <span class='detail-icon'>{$priorityEmoji}</span>
                                            <span class='detail-label'>Priority:</span>
                                            <span class='detail-value' style='color: {$priorityColor}; font-weight: 600;'>" . ucfirst($task->priority) . "</span>
                                        </div>";
                        
                        if ($task->description) {
                            $emailBody .= "
                                        
                                        <div class='description'>
                                            <strong>📝 Description:</strong><br>
                                            {$task->description}
                                        </div>";
                        }
                        
                        $emailBody .= "
                                    </div>
                                    
                                    <div class='success-note'>
                                        ✅ This task has been automatically added to your Google Calendar
                                    </div>
                                    
                                    <div style='text-align: center;'>
                                        <a href='https://calendar.google.com' class='calendar-button'>📅 Open Google Calendar</a>
                                    </div>
                                </div>
                                <div class='footer'>
                                    <p><strong>FlowSpec AI</strong> - Automated Workflow Management</p>
                                    <p>Powered by Gemini AI & Google Workspace</p>
                                </div>
                            </div>
                        </body>
                        </html>
                        ";
                        
                        $this->gmailService->sendEmail(
                            $user->email,
                            "✅ New Task Created: {$task->title}",
                            $emailBody
                        );
                        
                        \Log::info('Email notification sent', [
                            'task_id' => $task->id,
                            'to' => $user->email
                        ]);
                        
                        $response .= "\n📧 Email notification sent!";
                    } else {
                        \Log::warning('Failed to initialize Gmail service', [
                            'task_id' => $task->id
                        ]);
                    }
                } else {
                    \Log::warning('Failed to initialize Calendar service', [
                        'task_id' => $task->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to sync task to Google Calendar/Gmail', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $response .= "\n⚠️ Note: Task created but couldn't sync to Google services. Error: " . $e->getMessage();
            }
        } else {
            \Log::info('Skipping Google sync', [
                'task_id' => $task->id,
                'reason' => !$user->google_access_token ? 'No Google token' : 'No due date'
            ]);
        }

        return [
            'success' => true,
            'action' => 'task_created',
            'response' => $response,
            'workflow_spec' => [
                'action' => 'create',
                'resource' => 'task',
                'data' => $task->toArray(),
            ],
        ];
    }

    protected function listTasks(array $entities, string $userId): array
    {
        $query = Task::where('user_id', $userId);
        
        if (!empty($entities['status'])) {
            $query->where('status', $entities['status']);
        }
        
        if (!empty($entities['priority'])) {
            $query->where('priority', $entities['priority']);
        }
        
        $tasks = $query->latest()->take(5)->get();
        
        if ($tasks->isEmpty()) {
            return [
                'success' => true,
                'action' => 'list_tasks',
                'response' => 'You have no tasks matching that criteria.',
            ];
        }
        
        $taskList = $tasks->map(function ($task) {
            return "• {$task->title} ({$task->status}, {$task->priority} priority)";
        })->implode("\n");
        
        return [
            'success' => true,
            'action' => 'list_tasks',
            'response' => "Here are your tasks:\n\n{$taskList}",
        ];
    }

    protected function updateTask(array $entities, string $userId): array
    {
        // Simple implementation - in production you'd need better task identification
        return [
            'success' => false,
            'action' => 'update_task_failed',
            'response' => 'To update a task, please go to the Tasks page and edit it directly.',
        ];
    }

    protected function deleteTask(array $entities, string $userId): array
    {
        return [
            'success' => false,
            'action' => 'delete_task_failed',
            'response' => 'To delete a task, please go to the Tasks page.',
        ];
    }

    protected function createMeeting(array $entities, string $userId): array
    {
        if (empty($entities['title']) || empty($entities['start_time'])) {
            return [
                'success' => false,
                'action' => 'create_meeting_failed',
                'response' => 'Please provide a title and start time for the meeting.',
                'error' => 'Missing required fields',
            ];
        }

        $user = auth()->user();
        
        $startTime = Carbon::parse($entities['start_time']);
        $endTime = isset($entities['end_time']) 
            ? Carbon::parse($entities['end_time']) 
            : $startTime->copy()->addHour();

        $meeting = Meeting::create([
            'user_id' => $userId,
            'title' => $entities['title'],
            'description' => $entities['description'] ?? null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'meeting_type' => $entities['meeting_type'] ?? 'online',
            'status' => 'scheduled',
        ]);

        $response = "📅 Meeting scheduled: \"{$meeting->title}\" at " . $startTime->format('M j, Y g:i A');
        
        // Sync to Google Calendar and send email invites
        if ($user->google_access_token) {
            try {
                if ($this->calendarService->setUserToken($user)) {
                    // Parse and validate attendees if provided
                    $attendees = [];
                    $validatedAttendees = []; // For email sending (exclude example.com)
                    
                    if (!empty($entities['attendees'])) {
                        $rawAttendees = is_array($entities['attendees']) 
                            ? $entities['attendees'] 
                            : explode(',', $entities['attendees']);
                        
                        // Validate and filter only valid emails
                        foreach ($rawAttendees as $email) {
                            $email = trim($email);
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $attendees[] = $email; // For Google Calendar
                                
                                // Skip example.com domains for actual email sending
                                $domain = substr(strrchr($email, "@"), 1);
                                if ($domain !== 'example.com') {
                                    $validatedAttendees[] = $email;
                                } else {
                                    Log::info('Skipping example.com email for sending', ['email' => $email]);
                                }
                            } else {
                                Log::warning('Invalid attendee email skipped', ['email' => $email]);
                            }
                        }
                    }
                    
                    $calendarEvent = $this->calendarService->createEvent(
                        $meeting->title,
                        $meeting->description ?? "Meeting created via FlowSpec AI",
                        $startTime,
                        $endTime,
                        $attendees,
                        true // Create Google Meet link
                    );
                    
                    // Get the Google Meet link
                    $meetLink = null;
                    if (isset($calendarEvent->conferenceData->entryPoints)) {
                        foreach ($calendarEvent->conferenceData->entryPoints as $entryPoint) {
                            if ($entryPoint->entryPointType === 'video') {
                                $meetLink = $entryPoint->uri;
                                break;
                            }
                        }
                    }
                    
                    // Store Google event ID
                    $meeting->update(['google_event_id' => $calendarEvent->id]);
                    
                    $response .= "\n📅 Added to Google Calendar!";
                    if ($meetLink) {
                        $response .= "\n🎥 Google Meet: $meetLink";
                    }
                    
                    // Send email invites to all attendees (exclude example.com)
                    if ($this->gmailService->setUserToken($user) && !empty($validatedAttendees)) {
                        $sentCount = 0;
                        foreach ($validatedAttendees as $attendeeEmail) {
                            try {
                                $duration = $startTime->diffInMinutes($endTime);
                                $emailBody = "
                                <html>
                                <head>
                                    <style>
                                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
                                        .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                                        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                                        .header p { margin: 5px 0 0 0; opacity: 0.9; }
                                        .content { padding: 30px; }
                                        .meeting-card { background: #f9fafb; border-left: 4px solid #667eea; padding: 20px; border-radius: 8px; margin: 20px 0; }
                                        .meeting-title { font-size: 22px; font-weight: 600; color: #1f2937; margin: 0 0 15px 0; }
                                        .detail-row { display: flex; margin: 12px 0; align-items: center; }
                                        .detail-icon { width: 24px; height: 24px; margin-right: 12px; }
                                        .detail-label { font-weight: 600; color: #4b5563; margin-right: 8px; }
                                        .detail-value { color: #1f2937; }
                                        .meet-button { display: inline-block; background: linear-gradient(135deg, #34D399 0%, #10B981 100%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; text-align: center; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); }
                                        .meet-button:hover { box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4); }
                                        .description { background: white; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; margin: 15px 0; color: #4b5563; }
                                        .footer { background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 13px; border-top: 1px solid #e5e7eb; }
                                        .organizer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
                                    </style>
                                </head>
                                <body>
                                    <div class='container'>
                                        <div class='header'>
                                            <h1>📅 Meeting Invitation</h1>
                                            <p>You've been invited to a meeting</p>
                                        </div>
                                        <div class='content'>
                                            <div class='meeting-card'>
                                                <h2 class='meeting-title'>{$meeting->title}</h2>
                                                
                                                <div class='detail-row'>
                                                    <span class='detail-icon'>📅</span>
                                                    <span class='detail-label'>Date:</span>
                                                    <span class='detail-value'>{$startTime->format('l, F j, Y')}</span>
                                                </div>
                                                
                                                <div class='detail-row'>
                                                    <span class='detail-icon'>⏰</span>
                                                    <span class='detail-label'>Time:</span>
                                                    <span class='detail-value'>{$startTime->format('g:i A')} - {$endTime->format('g:i A')} ({$duration} minutes)</span>
                                                </div>";
                                                
                                if ($meetLink) {
                                    $emailBody .= "
                                                
                                                <div style='text-align: center; margin: 25px 0;'>
                                                    <a href='{$meetLink}' class='meet-button'>🎥 Join Google Meet</a>
                                                </div>
                                                
                                                <div class='detail-row'>
                                                    <span class='detail-icon'>🔗</span>
                                                    <span class='detail-label'>Meeting Link:</span>
                                                    <span class='detail-value'><a href='{$meetLink}' style='color: #667eea;'>{$meetLink}</a></span>
                                                </div>";
                                }
                                
                                if ($meeting->description) {
                                    $emailBody .= "
                                                
                                                <div class='description'>
                                                    <strong>📝 Description:</strong><br>
                                                    {$meeting->description}
                                                </div>";
                                }
                                
                                $emailBody .= "
                                                
                                                <div class='organizer'>
                                                    <strong>Organized by:</strong> {$user->name}<br>
                                                    <strong>Email:</strong> {$user->email}
                                                </div>
                                            </div>
                                            
                                            <p style='color: #6b7280; font-size: 14px; margin-top: 20px;'>
                                                ✅ This event has been automatically added to your Google Calendar.
                                            </p>
                                        </div>
                                        <div class='footer'>
                                            <p><strong>FlowSpec AI</strong> - Automated Workflow Management</p>
                                            <p>Powered by Gemini AI & Google Workspace</p>
                                        </div>
                                    </div>
                                </body>
                                </html>
                                ";
                                
                                $this->gmailService->sendEmail(
                                    $attendeeEmail,
                                    "📅 Meeting Invitation: {$meeting->title}",
                                    $emailBody
                                );
                                
                                $sentCount++;
                                Log::info('Meeting invitation sent', [
                                    'to' => $attendeeEmail,
                                    'meeting' => $meeting->title
                                ]);
                            } catch (\Exception $emailError) {
                                Log::error('Failed to send meeting invite email', [
                                    'attendee' => $attendeeEmail,
                                    'error' => $emailError->getMessage()
                                ]);
                            }
                        }
                        
                        if ($sentCount > 0) {
                            $response .= "\n📧 Email invitations sent to {$sentCount} attendee(s)!";
                        }
                    } else if (!empty($attendees) && empty($validatedAttendees)) {
                        $response .= "\n⚠️ Note: Attendees added to calendar but emails skipped (example.com domains).";
                    }
                    
                    // Send confirmation email to organizer
                    if ($this->gmailService->setUserToken($user)) {
                        try {
                            $duration = $startTime->diffInMinutes($endTime);
                            $attendeesList = !empty($attendees) ? implode(', ', $attendees) : 'No attendees';
                            
                            $confirmEmailBody = "
                            <html>
                            <head>
                                <style>
                                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
                                    .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                                    .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px; text-align: center; }
                                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                                    .header p { margin: 5px 0 0 0; opacity: 0.9; }
                                    .content { padding: 30px; }
                                    .meeting-card { background: #f0fdf4; border-left: 4px solid #10B981; padding: 20px; border-radius: 8px; margin: 20px 0; }
                                    .meeting-title { font-size: 22px; font-weight: 600; color: #1f2937; margin: 0 0 15px 0; }
                                    .detail-row { display: flex; margin: 12px 0; align-items: center; }
                                    .detail-icon { width: 24px; height: 24px; margin-right: 12px; }
                                    .detail-label { font-weight: 600; color: #4b5563; margin-right: 8px; }
                                    .detail-value { color: #1f2937; }
                                    .meet-button { display: inline-block; background: linear-gradient(135deg, #34D399 0%, #10B981 100%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; text-align: center; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); }
                                    .success-badge { background: #d1fae5; color: #065f46; padding: 8px 16px; border-radius: 20px; display: inline-block; font-size: 14px; font-weight: 600; margin: 15px 0; }
                                    .description { background: white; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; margin: 15px 0; color: #4b5563; }
                                    .footer { background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 13px; border-top: 1px solid #e5e7eb; }
                                </style>
                            </head>
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>✅ Meeting Confirmed</h1>
                                        <p>Your meeting has been successfully scheduled</p>
                                    </div>
                                    <div class='content'>
                                        <div class='success-badge'>
                                            ✨ Successfully created and synced to Google Calendar
                                        </div>
                                        
                                        <div class='meeting-card'>
                                            <h2 class='meeting-title'>{$meeting->title}</h2>
                                            
                                            <div class='detail-row'>
                                                <span class='detail-icon'>📅</span>
                                                <span class='detail-label'>Date:</span>
                                                <span class='detail-value'>{$startTime->format('l, F j, Y')}</span>
                                            </div>
                                            
                                            <div class='detail-row'>
                                                <span class='detail-icon'>⏰</span>
                                                <span class='detail-label'>Time:</span>
                                                <span class='detail-value'>{$startTime->format('g:i A')} - {$endTime->format('g:i A')} ({$duration} minutes)</span>
                                            </div>
                                            
                                            <div class='detail-row'>
                                                <span class='detail-icon'>👥</span>
                                                <span class='detail-label'>Attendees:</span>
                                                <span class='detail-value'>{$attendeesList}</span>
                                            </div>";
                                            
                            if ($meetLink) {
                                $confirmEmailBody .= "
                                            
                                            <div style='text-align: center; margin: 25px 0;'>
                                                <a href='{$meetLink}' class='meet-button'>🎥 Join Google Meet</a>
                                            </div>
                                            
                                            <div class='detail-row'>
                                                <span class='detail-icon'>🔗</span>
                                                <span class='detail-label'>Meeting Link:</span>
                                                <span class='detail-value'><a href='{$meetLink}' style='color: #10B981;'>{$meetLink}</a></span>
                                            </div>";
                            }
                            
                            if ($meeting->description) {
                                $confirmEmailBody .= "
                                            
                                            <div class='description'>
                                                <strong>📝 Description:</strong><br>
                                                {$meeting->description}
                                            </div>";
                            }
                            
                            $confirmEmailBody .= "
                                        </div>
                                        
                                        <p style='color: #6b7280; font-size: 14px; margin-top: 20px;'>";
                            
                            if (!empty($attendees)) {
                                $confirmEmailBody .= "
                                            ✅ Added to your Google Calendar<br>
                                            📧 Email invitations sent to " . count($attendees) . " attendee(s)";
                            } else {
                                $confirmEmailBody .= "
                                            ✅ Added to your Google Calendar";
                            }
                            
                            $confirmEmailBody .= "
                                        </p>
                                    </div>
                                    <div class='footer'>
                                        <p><strong>FlowSpec AI</strong> - Automated Workflow Management</p>
                                        <p>Powered by Gemini AI & Google Workspace</p>
                                    </div>
                                </div>
                            </body>
                            </html>
                            ";
                            
                            $this->gmailService->sendEmail(
                                $user->email,
                                "✅ Meeting Confirmed: {$meeting->title}",
                                $confirmEmailBody
                            );
                        } catch (\Exception $emailError) {
                            Log::error('Failed to send confirmation email to organizer', [
                                'error' => $emailError->getMessage()
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to sync meeting to Google Calendar/Gmail', [
                    'meeting_id' => $meeting->id,
                    'error' => $e->getMessage()
                ]);
                $response .= "\n⚠️ Note: Meeting created but couldn't sync to Google services.";
            }
        }

        return [
            'success' => true,
            'action' => 'meeting_created',
            'response' => $response,
            'workflow_spec' => [
                'action' => 'create',
                'resource' => 'meeting',
                'data' => $meeting->toArray(),
            ],
        ];
    }

    protected function listMeetings(array $entities, string $userId): array
    {
        $query = Meeting::where('user_id', $userId);
        
        if (!empty($entities['status'])) {
            $query->where('status', $entities['status']);
        } else {
            // Default to upcoming meetings
            $query->where('start_time', '>', now());
        }
        
        $meetings = $query->orderBy('start_time')->take(5)->get();
        
        if ($meetings->isEmpty()) {
            return [
                'success' => true,
                'action' => 'list_meetings',
                'response' => 'You have no upcoming meetings.',
            ];
        }
        
        $meetingList = $meetings->map(function ($meeting) {
            $time = Carbon::parse($meeting->start_time)->format('M j, g:i A');
            return "• {$meeting->title} - {$time} ({$meeting->meeting_type})";
        })->implode("\n");
        
        return [
            'success' => true,
            'action' => 'list_meetings',
            'response' => "Here are your upcoming meetings:\n\n{$meetingList}",
        ];
    }

    protected function updateMeeting(array $entities, string $userId): array
    {
        return [
            'success' => false,
            'action' => 'update_meeting_failed',
            'response' => 'To update a meeting, please go to the Meetings page and edit it directly.',
        ];
    }

    protected function deleteMeeting(array $entities, string $userId): array
    {
        return [
            'success' => false,
            'action' => 'delete_meeting_failed',
            'response' => 'To cancel a meeting, please go to the Meetings page.',
        ];
    }

    /**
     * Clear chat history for the authenticated user
     */
    public function clearHistory(Request $request)
    {
        try {
            $userId = auth()->id();
            
            // Delete all NLP commands for this user
            $deletedCount = NLPCommand::where('user_id', $userId)->delete();
            
            Log::info('Chat history cleared', [
                'user_id' => $userId,
                'deleted_count' => $deletedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$deletedCount} message(s) from your chat history.",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear chat history', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single chat message
     */
    public function deleteMessage(Request $request, $id)
    {
        try {
            $userId = auth()->id();
            
            $command = NLPCommand::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$command) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found or you do not have permission to delete it.'
                ], 404);
            }

            $command->delete();
            
            Log::info('Chat message deleted', [
                'user_id' => $userId,
                'message_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete chat message', [
                'user_id' => auth()->id(),
                'message_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message: ' . $e->getMessage()
            ], 500);
        }
    }
}

