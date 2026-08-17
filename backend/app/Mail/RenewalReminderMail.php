<?php

namespace App\Mail;

use App\Models\FanclubMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public FanclubMember $member)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your KLP48 Fanclub membership is expiring soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.fanclub.renewal-reminder',
            with: [
                'name' => $this->member->name,
                'expiresAt' => $this->member->expires_at->format('d M Y'),
                'renewUrl' => rtrim(config('app.frontend_url'), '/') . '/fanclub/subscribe',
            ],
        );
    }
}
