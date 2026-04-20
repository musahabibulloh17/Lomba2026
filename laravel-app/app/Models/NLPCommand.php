<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NLPCommand extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nlp_commands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'command',
        'intent',
        'entities',
        'response',
        'action_taken',
        'success',
        'error_message',
        'processing_time',
        'workflow_spec',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entities' => 'array',
            'workflow_spec' => 'array',
            'success' => 'boolean',
            'processing_time' => 'integer',
        ];
    }

    /**
     * Get the user that owns the NLP command.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include successful commands.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope a query to only include failed commands.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope a query to filter by intent.
     */
    public function scopeByIntent($query, $intent)
    {
        return $query->where('intent', $intent);
    }
}
