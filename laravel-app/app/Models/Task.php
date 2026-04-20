<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'reminder_date',
        'reminder_sent',
        'tags',
        'assigned_to',
        'project_id',
        'completed_at',
        'google_event_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'reminder_date' => 'datetime',
            'reminder_sent' => 'boolean',
            'tags' => 'array',
            'assigned_to' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include tasks with upcoming reminders.
     */
    public function scopeUpcomingReminders($query)
    {
        return $query->where('reminder_sent', false)
            ->whereNotNull('reminder_date')
            ->where('reminder_date', '<=', now());
    }
}
