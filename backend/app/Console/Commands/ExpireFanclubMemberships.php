<?php

namespace App\Console\Commands;

use App\Models\FanclubMember;
use Illuminate\Console\Command;

class ExpireFanclubMemberships extends Command
{
    protected $signature   = 'fanclub:expire-memberships';
    protected $description = 'Flip active/cancelled fanclub memberships to expired once their expires_at date has passed';

    public function handle(): int
    {
        $count = FanclubMember::query()
            ->whereIn('status', ['active', 'cancelled'])
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} fanclub membership(s).");

        return self::SUCCESS;
    }
}
