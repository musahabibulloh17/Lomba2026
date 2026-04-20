<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($task) ? 'Edit Task' : 'Create Task' }} - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent flex items-center">
                <span class="material-icons text-4xl mr-3" style="color: #2563eb;">{{ isset($task) ? 'edit' : 'add_task' }}</span>
                {{ isset($task) ? 'Edit Task' : 'Create New Task' }}
            </h2>
            <p class="text-gray-600 mt-2 text-lg">{{ isset($task) ? 'Update task details below' : 'Fill in the details to create a new task' }}</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-start">
                    <span class="material-icons mr-3 mt-0.5">error</span>
                    <div class="flex-1">
                        <p class="font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-8">
            <form action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" method="POST">
                @csrf
                @if(isset($task))
                    @method('PUT')
                @endif

                <div class="mb-6">
                    <label for="title" class="flex items-center text-gray-700 font-semibold mb-2">
                        <span class="material-icons text-sm mr-2 text-indigo-600">title</span>
                        Title *
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $task->title ?? '') }}" required
                           placeholder="Enter task title..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                </div>

                <div class="mb-6">
                    <label for="description" class="flex items-center text-gray-700 font-semibold mb-2">
                        <span class="material-icons text-sm mr-2 text-indigo-600">description</span>
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Add task description..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">{{ old('description', $task->description ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="status" class="flex items-center text-gray-700 font-semibold mb-2">
                            <span class="material-icons text-sm mr-2 text-indigo-600">flag</span>
                            Status *
                        </label>
                        <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition appearance-none">
                            <option value="pending" {{ old('status', $task->status ?? 'pending') === 'pending' ? 'selected' : '' }}>⚪ Pending</option>
                            <option value="in_progress" {{ old('status', $task->status ?? '') === 'in_progress' ? 'selected' : '' }}>🔵 In Progress</option>
                            <option value="completed" {{ old('status', $task->status ?? '') === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                            <option value="cancelled" {{ old('status', $task->status ?? '') === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="flex items-center text-gray-700 font-semibold mb-2">
                            <span class="material-icons text-sm mr-2 text-indigo-600">priority_high</span>
                            Priority *
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition appearance-none">
                            <option value="low" {{ old('priority', $task->priority ?? 'medium') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                            <option value="medium" {{ old('priority', $task->priority ?? 'medium') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                            <option value="high" {{ old('priority', $task->priority ?? 'medium') === 'high' ? 'selected' : '' }}>🔴 High</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="due_date" class="flex items-center text-gray-700 font-semibold mb-2">
                            <span class="material-icons text-sm mr-2 text-indigo-600">event</span>
                            Due Date
                        </label>
                        <input type="datetime-local" name="due_date" id="due_date" 
                               value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                    </div>

                    <div>
                        <label for="reminder_date" class="flex items-center text-gray-700 font-semibold mb-2">
                            <span class="material-icons text-sm mr-2 text-indigo-600">notifications</span>
                            Reminder Date
                        </label>
                        <input type="datetime-local" name="reminder_date" id="reminder_date" 
                               value="{{ old('reminder_date', isset($task) && $task->reminder_date ? $task->reminder_date->format('Y-m-d\TH:i') : '') }}"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="project_id" class="flex items-center text-gray-700 font-semibold mb-2">
                        <span class="material-icons text-sm mr-2 text-indigo-600">folder</span>
                        Project ID
                    </label>
                    <input type="text" name="project_id" id="project_id" value="{{ old('project_id', $task->project_id ?? '') }}"
                           placeholder="Optional project ID..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition">
                        <span class="material-icons text-sm mr-2">close</span>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition transform hover:scale-105 shadow-lg">
                        <span class="material-icons text-sm mr-2">{{ isset($task) ? 'save' : 'add_circle' }}</span>
                        {{ isset($task) ? 'Update Task' : 'Create Task' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
