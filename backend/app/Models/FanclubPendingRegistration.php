<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FanclubPendingRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'tier',
        'amount_cents',
        'bill_code',
        'reference_no',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }
}
