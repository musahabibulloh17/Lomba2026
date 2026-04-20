<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($meeting) ? 'Edit Meeting' : 'Create Meeting' }} - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('layouts.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">{{ isset($meeting) ? 'Edit Meeting' : 'Create New Meeting' }}</h2>

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
            <form action="{{ isset($meeting) ? route('meetings.update', $meeting) : route('meetings.store') }}" method="POST">
                @csrf
                @if(isset($meeting))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $meeting->title ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('description', $meeting->description ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_time" class="block text-gray-700 font-medium mb-2">Start Time *</label>
                        <input type="datetime-local" name="start_time" id="start_time" required
                               value="{{ old('start_time', isset($meeting) && $meeting->start_time ? $meeting->start_time->format('Y-m-d\TH:i') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="end_time" class="block text-gray-700 font-medium mb-2">End Time *</label>
                        <input type="datetime-local" name="end_time" id="end_time" required
                               value="{{ old('end_time', isset($meeting) && $meeting->end_time ? $meeting->end_time->format('Y-m-d\TH:i') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="meeting_type" class="block text-gray-700 font-medium mb-2">Meeting Type *</label>
                        <select name="meeting_type" id="meeting_type" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="online" {{ old('meeting_type', $meeting->meeting_type ?? 'online') === 'online' ? 'selected' : '' }}>Online</option>
                            <option value="in-person" {{ old('meeting_type', $meeting->meeting_type ?? '') === 'in-person' ? 'selected' : '' }}>In-Person</option>
                            <option value="hybrid" {{ old('meeting_type', $meeting->meeting_type ?? '') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    @if(isset($meeting))
                        <div>
                            <label for="status" class="block text-gray-700 font-medium mb-2">Status *</label>
                            <select name="status" id="status" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="scheduled" {{ old('status', $meeting->status ?? 'scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="completed" {{ old('status', $meeting->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $meeting->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    @else
                        <div>
                            <label for="reminder_minutes" class="block text-gray-700 font-medium mb-2">Reminder (minutes before)</label>
                            <input type="number" name="reminder_minutes" id="reminder_minutes" min="0"
                                   value="{{ old('reminder_minutes', $meeting->reminder_minutes ?? 15) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="location" class="block text-gray-700 font-medium mb-2">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $meeting->location ?? '') }}"
                           placeholder="Office, Room 101, etc."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="meeting_link" class="block text-gray-700 font-medium mb-2">Meeting Link</label>
                    <input type="url" name="meeting_link" id="meeting_link" value="{{ old('meeting_link', $meeting->meeting_link ?? '') }}"
                           placeholder="https://meet.google.com/abc-defg-hij"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Zoom, Google Meet, Teams, etc.</p>
                </div>

                @if(isset($meeting))
                    <div class="mb-4">
                        <label for="reminder_minutes" class="block text-gray-700 font-medium mb-2">Reminder (minutes before)</label>
                        <input type="number" name="reminder_minutes" id="reminder_minutes" min="0"
                               value="{{ old('reminder_minutes', $meeting->reminder_minutes ?? 15) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                @endif

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('meetings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">
                        {{ isset($meeting) ? 'Update Meeting' : 'Create Meeting' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
