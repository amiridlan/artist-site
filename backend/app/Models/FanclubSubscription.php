<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanclubSubscription extends Model
{
    protected $fillable = [
        'fanclub_member_id',
        'tier',
        'amount_cents',
        'bill_code',
        'transaction_id',
        'reference_no',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(FanclubMember::class, 'fanclub_member_id');
    }
}
