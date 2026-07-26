<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ConflictLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'conflictable_type',
        'conflictable_id',
        'conflict_type',
        'details',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'details' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the parent conflictable model (ScheduleEvent or KanbanCard).
     */
    public function conflictable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who resolved this conflict.
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
