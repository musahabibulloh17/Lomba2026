<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\EmailLog;
use App\Models\NLPCommand;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $stats = [
            'total_tasks' => Task::where('user_id', $userId)->count(),
            'total_meetings' => Meeting::where('user_id', $userId)->count(),
            'pending_tasks' => Task::where('user_id', $userId)->where('status', 'pending')->count(),
            'upcoming_meetings' => Meeting::where('user_id', $userId)
                ->where('status', 'scheduled')
                ->where('start_time', '>', now())
                ->count(),
            'completed_tasks' => Task::where('user_id', $userId)->where('status', 'completed')->count(),
            'total_nlp_commands' => NLPCommand::where('user_id', $userId)->count(),
        ];
        
        // Recent activities
        $recentTasks = Task::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $upcomingMeetings = Meeting::where('user_id', $userId)
            ->where('status', 'scheduled')
            ->where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact('stats', 'recentTasks', 'upcomingMeetings'));
    }
    
    public function settings()
    {
        return view('dashboard.settings');
    }
}
