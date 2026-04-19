<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaSnapshot extends Model
{
    protected $fillable = [
        'platform_id', 'followers', 'views', 'posts',
        'likes', 'comments', 'extra_data', 'fetched_at',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'platform_id');
    }
}
