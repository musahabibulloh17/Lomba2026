<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($task) ? 'Edit Task' : 'Create Task' }} - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('layouts.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">{{ isset($task) ? 'Edit Task' : 'Create New Task' }}</h2>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" method="POST">
                @csrf
                @if(isset($task))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $task->title ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $task->description ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="block text-gray-700 font-medium mb-2">Status *</label>
                        <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="pending" {{ old('status', $task->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $task->status ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $task->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $task->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-gray-700 font-medium mb-2">Priority *</label>
                        <select name="priority" id="priority" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low" {{ old('priority', $task->priority ?? 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority ?? 'medium') === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="due_date" class="block text-gray-700 font-medium mb-2">Due Date</label>
                        <input type="datetime-local" name="due_date" id="due_date" 
                               value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="reminder_date" class="block text-gray-700 font-medium mb-2">Reminder Date</label>
                        <input type="datetime-local" name="reminder_date" id="reminder_date" 
                               value="{{ old('reminder_date', isset($task) && $task->reminder_date ? $task->reminder_date->format('Y-m-d\TH:i') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="project_id" class="block text-gray-700 font-medium mb-2">Project ID</label>
                    <input type="text" name="project_id" id="project_id" value="{{ old('project_id', $task->project_id ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('tasks.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        {{ isset($task) ? 'Update Task' : 'Create Task' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
