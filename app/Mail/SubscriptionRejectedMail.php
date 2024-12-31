<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class SubscriptionRejectedMail extends Mailable
{
    use SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Premium Subscription Request - StoryTail'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-rejected',
            with: [
                'user' => $this->user,
                'logo' => public_path('images/logo-storyTail.png')
            ]
        );
    }

    public function attachments(): array
    {
        $logoPath = public_path('images/logo-storyTail.png');

        if (!file_exists($logoPath)) {
            return [];
        }

        return [
            Attachment::fromPath($logoPath)
                ->as('logo-storyTail.png')
                ->withMime('image/png'),
        ];
    }
}
