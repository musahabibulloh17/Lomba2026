<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meetings - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    @include('layouts.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Meetings</h2>
            <a href="{{ route('meetings.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition">
                + New Meeting
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('meetings.index') }}" class="flex flex-wrap gap-4">
                <select name="status" class="border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <select name="meeting_type" class="border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">All Types</option>
                    <option value="in-person" {{ request('meeting_type') === 'in-person' ? 'selected' : '' }}>In-Person</option>
                    <option value="online" {{ request('meeting_type') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="hybrid" {{ request('meeting_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
                <input type="text" name="search" placeholder="Search meetings..." value="{{ request('search') }}" class="border border-gray-300 rounded-lg px-4 py-2 flex-1">
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                    Filter
                </button>
                <a href="{{ route('meetings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    Reset
                </a>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Meetings List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($meetings->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    No meetings found. Schedule your first meeting!
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($meetings as $meeting)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $meeting->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($meeting->description, 50) }}</div>
                                    @if($meeting->location)
                                        <div class="text-xs text-gray-400 mt-1">📍 {{ $meeting->location }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $meeting->start_time->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $meeting->start_time->format('h:i A') }} - {{ $meeting->end_time->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs rounded-full 
                                        @if($meeting->meeting_type === 'online') bg-blue-100 text-blue-800
                                        @elseif($meeting->meeting_type === 'in-person') bg-purple-100 text-purple-800
                                        @else bg-indigo-100 text-indigo-800
                                        @endif">
                                        {{ ucfirst(str_replace('-', ' ', $meeting->meeting_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs rounded-full 
                                        @if($meeting->status === 'completed') bg-green-100 text-green-800
                                        @elseif($meeting->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($meeting->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    @if($meeting->meeting_link)
                                        <a href="{{ $meeting->meeting_link }}" target="_blank" class="text-green-600 hover:text-green-900 mr-3">Join</a>
                                    @endif
                                    <a href="{{ route('meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('meetings.edit', $meeting) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('meetings.destroy', $meeting) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
