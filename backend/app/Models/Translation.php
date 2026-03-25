<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'field',
        'value',
        'auto_translated',
    ];

    protected $casts = [
        'auto_translated' => 'boolean',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
