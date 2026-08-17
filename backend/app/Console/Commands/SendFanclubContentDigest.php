<?php

namespace App\Console\Commands;

use App\Mail\WeeklyContentDigestMail;
use App\Models\Event;
use App\Models\FanclubMember;
use App\Models\News;
use App\Models\Release;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFanclubContentDigest extends Command
{
    protected $signature = 'fanclub:send-content-digest';
    protected $description = 'Email active fanclub members a weekly digest of new events, releases, and news';

    public function handle(): int
    {
        $events = Event::where('status', 'upcoming')
            ->whereBetween('date', [now(), now()->addDays(7)])
            ->get();

        $releases = Release::where('release_date', '>=', now()->subDays(7))->get();

        $news = News::where('published', true)
            ->where('date', '>=', now()->subDays(7))
            ->get();

        if ($events->isEmpty() && $releases->isEmpty() && $news->isEmpty()) {
            $this->info('Nothing new this week — skipping digest.');
            return self::SUCCESS;
        }

        $members = FanclubMember::active()->get();

        foreach ($members as $member) {
            Mail::to($member->email)->queue(new WeeklyContentDigestMail($member, $events, $releases, $news));
        }

        $this->info("Weekly content digest queued for {$members->count()} member(s).");

        return self::SUCCESS;
    }
}
