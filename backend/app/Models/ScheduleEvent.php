<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'start_datetime',
        'end_datetime',
        'venue',
        'status',
        'kanban_card_id',
        'created_by',
        'conflict_notes',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    /**
     * Get the members assigned to this event.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_schedule_event');
    }

    /**
     * Get the staff users assigned to this event.
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'schedule_event_user');
    }

    /**
     * Get the resources assigned to this event.
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'resource_schedule_event')
            ->withPivot('quantity');
    }

    /**
     * Get the kanban card associated with this event.
     */
    public function kanbanCard(): BelongsTo
    {
        return $this->belongsTo(KanbanCard::class);
    }

    /**
     * Get the user who created this event.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the conflict logs for this event.
     */
    public function conflictLogs(): MorphMany
    {
        return $this->morphMany(ConflictLog::class, 'conflictable');
    }

    /**
     * Scope query to confirmed events only.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope query to events for a specific member.
     */
    public function scopeForMember($query, $memberId)
    {
        return $query->whereHas('members', function ($q) use ($memberId) {
            $q->where('members.id', $memberId);
        });
    }

    /**
     * Scope query to events for a specific staff user.
     */
    public function scopeForStaff($query, $userId)
    {
        return $query->whereHas('staff', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * Scope query to events overlapping with a time range.
     */
    public function scopeOverlapping($query, $startDatetime, $endDatetime)
    {
        return $query->where(function ($q) use ($startDatetime, $endDatetime) {
            $q->whereBetween('start_datetime', [$startDatetime, $endDatetime])
                ->orWhereBetween('end_datetime', [$startDatetime, $endDatetime])
                ->orWhere(function ($q) use ($startDatetime, $endDatetime) {
                    $q->where('start_datetime', '<=', $startDatetime)
                        ->where('end_datetime', '>=', $endDatetime);
                });
        });
    }
}
