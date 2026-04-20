<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent flex items-center">
                    <span class="material-icons text-4xl mr-3" style="color: #2563eb;">task_alt</span>
                    My Tasks
                </h2>
                <p class="text-gray-600 mt-2 text-lg">Manage and track your tasks efficiently</p>
            </div>
            <a href="{{ route('tasks.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition transform hover:scale-105 shadow-lg flex items-center space-x-2">
                <span class="material-icons">add_circle</span>
                <span>New Task</span>
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
            <div class="flex items-center mb-4">
                <span class="material-icons text-indigo-600 mr-2">filter_list</span>
                <h3 class="text-lg font-bold text-gray-800">Filter Tasks</h3>
            </div>
            <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">flag</span>
                    <select name="status" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white shadow-sm transition appearance-none">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">priority_high</span>
                    <select name="priority" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white shadow-sm transition appearance-none">
                        <option value="">All Priority</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="relative md:col-span-2">
                    <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">search</span>
                    <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white shadow-sm transition">
                </div>
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center justify-center space-x-2 shadow-lg">
                    <span class="material-icons">search</span>
                    <span>Filter</span>
                </button>
                <a href="{{ route('tasks.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition flex items-center justify-center space-x-2">
                    <span class="material-icons">refresh</span>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-sm flex items-center">
                <span class="material-icons mr-3">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tasks List -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            @if($tasks->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full mb-6">
                        <span class="material-icons text-blue-600 text-6xl">assignment</span>
                    </div>
                    <p class="text-xl font-semibold text-gray-700 mb-2">No tasks found</p>
                    <p class="text-gray-500 mb-6">Get started by creating your first task!</p>
                    <a href="{{ route('tasks.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition transform hover:scale-105 shadow-lg">
                        <span class="material-icons">add_circle</span>
                        <span>Create Task</span>
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <span class="material-icons text-sm mr-2">title</span>
                                        Task
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <span class="material-icons text-sm mr-2">flag</span>
                                        Status
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <span class="material-icons text-sm mr-2">priority_high</span>
                                        Priority
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <span class="material-icons text-sm mr-2">event</span>
                                        Due Date
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <span class="material-icons text-sm mr-2">settings</span>
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($tasks as $task)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mr-3">
                                                @if($task->status === 'completed')
                                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                        <span class="material-icons text-green-600">check_circle</span>
                                                    </div>
                                                @elseif($task->status === 'in_progress')
                                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <span class="material-icons text-blue-600">pending</span>
                                                    </div>
                                                @elseif($task->status === 'cancelled')
                                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                        <span class="material-icons text-red-600">cancel</span>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <span class="material-icons text-gray-600">radio_button_unchecked</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-sm font-semibold text-gray-900">{{ $task->title }}</div>
                                                @if($task->description)
                                                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($task->description, 60) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($task->status === 'completed') bg-green-100 text-green-800
                                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($task->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($task->priority === 'high') bg-red-100 text-red-800
                                            @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            @if($task->priority === 'high')
                                                <span class="material-icons text-xs mr-1">priority_high</span>
                                            @elseif($task->priority === 'medium')
                                                <span class="material-icons text-xs mr-1">remove</span>
                                            @else
                                                <span class="material-icons text-xs mr-1">arrow_downward</span>
                                            @endif
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($task->due_date)
                                            <div class="flex items-center text-sm text-gray-700">
                                                <span class="material-icons text-sm mr-1 text-gray-400">event</span>
                                                {{ $task->due_date->format('M d, Y') }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">No due date</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">
                                                <span class="material-icons text-sm mr-1">visibility</span>
                                                View
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                                <span class="material-icons text-sm mr-1">edit</span>
                                                Edit
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-semibold transition">
                                                    <span class="material-icons text-sm mr-1">delete</span>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
