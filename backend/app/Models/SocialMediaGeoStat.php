<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaGeoStat extends Model
{
    protected $fillable = [
        'platform_id', 'country_code', 'country_name',
        'value', 'percentage', 'fetched_at',
    ];

    protected $casts = ['fetched_at' => 'datetime'];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'platform_id');
    }
}
