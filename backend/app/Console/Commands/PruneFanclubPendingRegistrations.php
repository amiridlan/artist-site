<?php

namespace App\Console\Commands;

use App\Models\FanclubPendingRegistration;
use Illuminate\Console\Command;

class PruneFanclubPendingRegistrations extends Command
{
    protected $signature   = 'fanclub:prune-pending-registrations';
    protected $description = 'Delete abandoned fanclub registrations (checkout started, payment never completed) so the email address can be used again';

    /**
     * How long to wait before treating an unpaid registration as abandoned.
     * Generous window — long enough that a slow/retried payment isn't
     * pruned out from under a genuine in-progress checkout.
     */
    private const ABANDONED_AFTER_HOURS = 48;

    public function handle(): int
    {
        $count = FanclubPendingRegistration::query()
            ->whereNull('processed_at')
            ->where('created_at', '<', now()->subHours(self::ABANDONED_AFTER_HOURS))
            ->delete();

        $this->info("Pruned {$count} abandoned fanclub registration(s).");

        return self::SUCCESS;
    }
}
