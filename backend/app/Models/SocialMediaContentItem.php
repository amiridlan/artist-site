<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaContentItem extends Model
{
    protected $fillable = [
        'platform_id', 'content_id', 'type', 'title',
        'thumbnail_url', 'published_at', 'metrics', 'fetched_at',
    ];

    protected $casts = [
        'metrics'      => 'array',
        'published_at' => 'datetime',
        'fetched_at'   => 'datetime',
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'platform_id');
    }
}
