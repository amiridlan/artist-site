<?php

namespace App\Console\Commands;

use App\Mail\RenewalReminderMail;
use App\Models\FanclubMember;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFanclubRenewalReminders extends Command
{
    protected $signature = 'fanclub:send-renewal-reminders';
    protected $description = 'Email active fanclub members whose membership expires within the reminder window';

    public function handle(): int
    {
        $days = config('fanclub.renewal_reminder_days', 30);
        $windowEnd = now()->addDays($days);

        $members = FanclubMember::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now()->toDateString(), $windowEnd->toDateString()])
            ->get()
            ->filter(function (FanclubMember $member) use ($days) {
                if ($member->renewal_reminder_sent_at === null) {
                    return true;
                }
                $staleBefore = $member->expires_at->copy()->subDays($days);
                return $member->renewal_reminder_sent_at->lt($staleBefore);
            });

        foreach ($members as $member) {
            Mail::to($member->email)->queue(new RenewalReminderMail($member));
            $member->update(['renewal_reminder_sent_at' => now()]);
        }

        $this->info("Queued {$members->count()} renewal reminder(s).");

        return self::SUCCESS;
    }
}
