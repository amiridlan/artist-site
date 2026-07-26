<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the schedule events using this resource.
     */
    public function scheduleEvents(): BelongsToMany
    {
        return $this->belongsToMany(ScheduleEvent::class, 'resource_schedule_event')
            ->withPivot('quantity');
    }

    /**
     * Scope query to active resources only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
