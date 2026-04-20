<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }
        .action-btn {
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Welcome back, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="text-gray-600 mt-2 text-lg">Here's what's happening with your workflow today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Tasks -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Tasks</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_tasks'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">All tasks</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl p-3 shadow-sm">
                        <span class="material-icons text-blue-600 text-3xl">assignment</span>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium mb-1">Pending Tasks</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">Need attention</p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl p-3 shadow-sm">
                        <span class="material-icons text-yellow-600 text-3xl">pending_actions</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Meetings -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium mb-1">Upcoming Meetings</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['upcoming_meetings'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">Scheduled</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-xl p-3 shadow-sm">
                        <span class="material-icons text-green-600 text-3xl">event_available</span>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="stat-card bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium mb-1">Completed Tasks</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['completed_tasks'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">Well done!</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl p-3 shadow-sm">
                        <span class="material-icons text-purple-600 text-3xl">check_circle</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center mb-5">
                <span class="material-icons text-indigo-600 mr-2">flash_on</span>
                <h3 class="text-xl font-bold text-gray-800">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('tasks.create') }}" 
                   class="action-btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-4 rounded-xl text-center font-semibold shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                    <span class="material-icons">add_task</span>
                    <span>Create New Task</span>
                </a>
                <a href="{{ route('meetings.create') }}" 
                   class="action-btn bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-4 rounded-xl text-center font-semibold shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                    <span class="material-icons">event_note</span>
                    <span>Schedule Meeting</span>
                </a>
                <a href="{{ route('chat') }}" 
                   class="action-btn bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-4 rounded-xl text-center font-semibold shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                    <span class="material-icons">smart_toy</span>
                    <span>AI Chat Assistant</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Tasks -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <span class="material-icons text-indigo-600 mr-2">list_alt</span>
                        <h3 class="text-xl font-bold text-gray-800">Recent Tasks</h3>
                    </div>
                    <span class="bg-indigo-100 text-indigo-600 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $recentTasks->count() }} Tasks
                    </span>
                </div>
                @if($recentTasks->isEmpty())
                    <div class="text-center py-8">
                        <span class="material-icons text-gray-300 text-6xl mb-3">task_alt</span>
                        <p class="text-gray-500">No tasks yet. Create your first task!</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentTasks as $task)
                            <div class="border-l-4 @if($task->status === 'completed') border-green-500 bg-green-50/50 @elseif($task->status === 'in_progress') border-blue-500 bg-blue-50/50 @else border-gray-300 bg-gray-50/50 @endif rounded-r-xl pl-4 pr-3 py-3 hover:shadow-md transition duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 mr-2">
                                        <h4 class="font-semibold text-gray-800 mb-1">{{ $task->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($task->description, 50) }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="material-icons text-xs mr-1">schedule</span>
                                            <span>Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 text-xs font-semibold rounded-lg whitespace-nowrap
                                        @if($task->status === 'completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ route('tasks.index') }}" 
                           class="text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center group">
                            <span>View All Tasks</span>
                            <span class="material-icons ml-1 transform group-hover:translate-x-1 transition duration-200">arrow_forward</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Upcoming Meetings -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <span class="material-icons text-indigo-600 mr-2">event</span>
                        <h3 class="text-xl font-bold text-gray-800">Upcoming Meetings</h3>
                    </div>
                    <span class="bg-purple-100 text-purple-600 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $upcomingMeetings->count() }} Meetings
                    </span>
                </div>
                @if($upcomingMeetings->isEmpty())
                    <div class="text-center py-8">
                        <span class="material-icons text-gray-300 text-6xl mb-3">event_available</span>
                        <p class="text-gray-500">No upcoming meetings. Schedule one!</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingMeetings as $meeting)
                            <div class="border-l-4 border-indigo-500 bg-indigo-50/50 rounded-r-xl pl-4 pr-3 py-3 hover:shadow-md transition duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 mb-1">{{ $meeting->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($meeting->description, 50) }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="material-icons text-xs mr-1">access_time</span>
                                            <span>{{ $meeting->start_time->format('M d, Y h:i A') }}</span>
                                        </div>
                                    </div>
                                    <span class="material-icons text-indigo-500">calendar_today</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ route('meetings.index') }}" 
                           class="text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center group">
                            <span>View All Meetings</span>
                            <span class="material-icons ml-1 transform group-hover:translate-x-1 transition duration-200">arrow_forward</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
