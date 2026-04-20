<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query()->where('user_id', auth()->id());

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('from_date')) {
            $query->where('due_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('due_date', '<=', $request->to_date);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'reminder_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'assigned_to' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::create([
            'user_id' => auth()->id(),
            ...$request->all()
        ]);

        Log::info('Task created', ['task_id' => $task->id, 'user_id' => auth()->id()]);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'Task created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'reminder_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'assigned_to' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Set completed_at if status is completed
        if (isset($data['status']) && $data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $task->update($data);

        Log::info('Task updated', ['task_id' => $task->id, 'user_id' => auth()->id()]);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'Task updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task->delete();

        Log::info('Task deleted', ['task_id' => $id, 'user_id' => auth()->id()]);

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Get upcoming reminders
     */
    public function upcomingReminders()
    {
        $tasks = Task::where('user_id', auth()->id())
            ->upcomingReminders()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
}
