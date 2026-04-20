<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No user found. Please create a user first.');
            return;
        }

        // Create sample tasks
        Task::create([
            'user_id' => $user->id,
            'title' => 'Complete Laravel Migration',
            'description' => 'Migrate all features from Node.js to Laravel framework',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(2),
        ]);

        Task::create([
            'user_id' => $user->id,
            'title' => 'Setup CI/CD Pipeline',
            'description' => 'Configure GitHub Actions for automated deployment',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(5),
        ]);

        Task::create([
            'user_id' => $user->id,
            'title' => 'Write Documentation',
            'description' => 'Update all documentation to reflect new Laravel structure',
            'status' => 'completed',
            'priority' => 'low',
            'due_date' => now()->subDays(1),
            'completed_at' => now()->subDays(1),
        ]);

        Task::create([
            'user_id' => $user->id,
            'title' => 'Implement NLP Chat',
            'description' => 'Build chat interface with Gemini AI integration',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
            'reminder_date' => now()->addDays(2),
        ]);

        // Create sample meetings
        Meeting::create([
            'user_id' => $user->id,
            'title' => 'Sprint Planning Meeting',
            'description' => 'Plan tasks for next sprint',
            'start_time' => now()->addDays(1)->setTime(10, 0),
            'end_time' => now()->addDays(1)->setTime(11, 0),
            'meeting_type' => 'online',
            'status' => 'scheduled',
            'reminder_minutes' => 15,
        ]);

        Meeting::create([
            'user_id' => $user->id,
            'title' => 'Client Demo',
            'description' => 'Demonstrate new features to client',
            'start_time' => now()->addDays(3)->setTime(14, 0),
            'end_time' => now()->addDays(3)->setTime(15, 30),
            'meeting_type' => 'online',
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'reminder_minutes' => 30,
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('Created 4 tasks and 2 meetings for user: ' . $user->name);
    }
}
