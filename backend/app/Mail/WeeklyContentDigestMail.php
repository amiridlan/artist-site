<?php

namespace App\Mail;

use App\Models\FanclubMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyContentDigestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public FanclubMember $member,
        public Collection $events,
        public Collection $releases,
        public Collection $news,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "What's new at KLP48 this week",
        );
    }

    public function content(): Content
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        return new Content(
            markdown: 'emails.fanclub.weekly-digest',
            with: [
                'name' => $this->member->name,
                'events' => $this->events,
                'releases' => $this->releases,
                'news' => $this->news,
                'frontendUrl' => $frontendUrl,
            ],
        );
    }
}
