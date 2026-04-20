<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MeetingController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Meeting::where('user_id', auth()->id());

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('meeting_type')) {
            $query->where('meeting_type', $request->meeting_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ilike', '%' . $request->search . '%')
                  ->orWhere('description', 'ilike', '%' . $request->search . '%');
            });
        }

        $meetings = $query->orderBy('start_time', 'asc')->paginate(15);

        return view('meetings.index', compact('meetings'));
    }

    public function create()
    {
        return view('meetings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'meeting_type' => 'required|in:in-person,online,hybrid',
            'meeting_link' => 'nullable|url',
            'reminder_minutes' => 'nullable|integer|min:0',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'scheduled';

        Meeting::create($validated);

        return redirect()->route('meetings.index')->with('success', 'Meeting created successfully!');
    }

    public function show(Meeting $meeting)
    {
        $this->authorize('view', $meeting);
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        return view('meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $this->authorize('update', $meeting);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'meeting_type' => 'required|in:in-person,online,hybrid',
            'meeting_link' => 'nullable|url',
            'status' => 'required|in:scheduled,completed,cancelled',
            'reminder_minutes' => 'nullable|integer|min:0',
        ]);

        $meeting->update($validated);

        return redirect()->route('meetings.index')->with('success', 'Meeting updated successfully!');
    }

    public function destroy(Meeting $meeting)
    {
        $this->authorize('delete', $meeting);
        $meeting->delete();

        return redirect()->route('meetings.index')->with('success', 'Meeting deleted successfully!');
    }
}
