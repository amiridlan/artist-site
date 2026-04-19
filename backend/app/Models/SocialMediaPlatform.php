<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocialMediaPlatform extends Model
{
    protected $fillable = ['platform', 'display_name', 'handle', 'channel_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function snapshots(): HasMany
    {
        return $this->hasMany(SocialMediaSnapshot::class, 'platform_id');
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(SocialMediaSnapshot::class, 'platform_id')->latestOfMany('fetched_at');
    }
}
