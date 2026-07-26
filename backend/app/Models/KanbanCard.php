<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KanbanCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'stage',
        'due_date',
        'position',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Get the members assigned to this card.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'kanban_card_member');
    }

    /**
     * Get the staff users assigned to this card.
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kanban_card_user');
    }

    /**
     * Get the schedule event created from this card.
     */
    public function scheduleEvent(): HasOne
    {
        return $this->hasOne(ScheduleEvent::class);
    }

    /**
     * Get the user who created this card.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the conflict logs for this card.
     */
    public function conflictLogs(): MorphMany
    {
        return $this->morphMany(ConflictLog::class, 'conflictable');
    }

    /**
     * Scope query to cards in a specific stage.
     */
    public function scopeInStage($query, $stage)
    {
        return $query->where('stage', $stage)->orderBy('position');
    }
}
