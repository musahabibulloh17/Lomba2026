<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
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
        'start_time',
        'end_time',
        'location',
        'meeting_type',
        'meeting_link',
        'attendees',
        'google_event_id',
        'reminder_minutes',
        'reminder_sent',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'attendees' => 'array',
            'reminder_sent' => 'boolean',
            'reminder_minutes' => 'integer',
        ];
    }

    /**
     * Get the user that owns the meeting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include scheduled meetings.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include upcoming meetings.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('start_time', '>', now());
    }

    /**
     * Scope a query to only include meetings that need reminders.
     */
    public function scopeNeedingReminder($query)
    {
        return $query->where('reminder_sent', false)
            ->where('status', 'scheduled')
            ->whereRaw('start_time - (reminder_minutes || \' minutes\')::interval <= ?', [now()]);
    }
}
