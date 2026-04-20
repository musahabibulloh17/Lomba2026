<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Meeting;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for tasks and meetings due within 30 minutes';

    protected $fonnteService;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fonnteService = app(FonnteService::class);
        
        $this->info('🔍 Checking for upcoming tasks and meetings...');
        
        $now = Carbon::now();
        $reminderWindow = $now->copy()->addMinutes(30);
        
        // Check tasks
        $this->checkTasks($now, $reminderWindow);
        
        // Check meetings
        $this->checkMeetings($now, $reminderWindow);
        
        $this->info('✅ WhatsApp reminder check completed!');
    }

    /**
     * Check and send reminders for tasks
     */
    protected function checkTasks($now, $reminderWindow)
    {
        $tasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$now, $reminderWindow])
            ->whereHas('user', function($query) {
                $query->whereNotNull('phone_number')
                      ->where('whatsapp_notifications', true);
            })
            ->with('user')
            ->get();

        $this->info("📋 Found {$tasks->count()} task(s) needing reminder");

        foreach ($tasks as $task) {
            // Check if we already sent a reminder recently (within last 25 minutes)
            $lastReminderKey = "task_reminder_{$task->id}";
            $lastReminder = cache($lastReminderKey);
            
            if ($lastReminder) {
                $this->warn("⏭️  Skipping task #{$task->id} - reminder already sent recently");
                continue;
            }

            $user = $task->user;
            $minutesLeft = $now->diffInMinutes($task->due_date, false);

            $this->info("📤 Sending reminder for task: {$task->title} to {$user->phone_number}");
            $this->info("   ⏰ Due in: {$minutesLeft} minutes");

            try {
                $result = $this->fonnteService->sendTaskReminder($user->phone_number, $task);
                
                if ($result['success']) {
                    $this->info("   ✅ Reminder sent successfully!");
                    
                    // Cache that we sent this reminder (expires in 25 minutes)
                    cache([$lastReminderKey => true], now()->addMinutes(25));
                    
                    Log::info('WhatsApp task reminder sent', [
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'phone' => $user->phone_number
                    ]);
                } else {
                    $this->error("   ❌ Failed to send reminder: {$result['message']}");
                    
                    Log::error('Failed to send WhatsApp task reminder', [
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'error' => $result['message']
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Exception: {$e->getMessage()}");
                
                Log::error('Exception sending WhatsApp task reminder', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Check and send reminders for meetings
     */
    protected function checkMeetings($now, $reminderWindow)
    {
        $meetings = Meeting::where('status', '!=', 'cancelled')
            ->whereNotNull('start_time')
            ->whereBetween('start_time', [$now, $reminderWindow])
            ->whereHas('user', function($query) {
                $query->whereNotNull('phone_number')
                      ->where('whatsapp_notifications', true);
            })
            ->with('user')
            ->get();

        $this->info("📅 Found {$meetings->count()} meeting(s) needing reminder");

        foreach ($meetings as $meeting) {
            // Check if we already sent a reminder recently (within last 25 minutes)
            $lastReminderKey = "meeting_reminder_{$meeting->id}";
            $lastReminder = cache($lastReminderKey);
            
            if ($lastReminder) {
                $this->warn("⏭️  Skipping meeting #{$meeting->id} - reminder already sent recently");
                continue;
            }

            $user = $meeting->user;
            $minutesLeft = $now->diffInMinutes($meeting->start_time, false);

            $this->info("📤 Sending reminder for meeting: {$meeting->title} to {$user->phone_number}");
            $this->info("   ⏰ Starts in: {$minutesLeft} minutes");

            try {
                $result = $this->fonnteService->sendMeetingReminder($user->phone_number, $meeting);
                
                if ($result['success']) {
                    $this->info("   ✅ Reminder sent successfully!");
                    
                    // Cache that we sent this reminder (expires in 25 minutes)
                    cache([$lastReminderKey => true], now()->addMinutes(25));
                    
                    Log::info('WhatsApp meeting reminder sent', [
                        'meeting_id' => $meeting->id,
                        'user_id' => $user->id,
                        'phone' => $user->phone_number
                    ]);
                } else {
                    $this->error("   ❌ Failed to send reminder: {$result['message']}");
                    
                    Log::error('Failed to send WhatsApp meeting reminder', [
                        'meeting_id' => $meeting->id,
                        'user_id' => $user->id,
                        'error' => $result['message']
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Exception: {$e->getMessage()}");
                
                Log::error('Exception sending WhatsApp meeting reminder', [
                    'meeting_id' => $meeting->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
