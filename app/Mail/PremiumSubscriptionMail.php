<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PremiumSubscriptionMail extends Mailable
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
            subject: 'Welcome to the Premium Plan - StoryTail'
        );
    }

    public function attachments(): array
    {
        $logoPath = public_path('images/logo-storyTail.png');

        if (!file_exists($logoPath)) {
            Log::warning('Logo file not found for PremiumSubscriptionMail');
            return [];
        }

        return [
            Attachment::fromPath($logoPath)
                ->as('logo-storyTail.png')
                ->withMime('image/png'),
        ];
    }

    public function content(): Content
    {
        $logoPath = public_path('images/logo-storyTail.png');

        return new Content(
            view: 'emails.premium-subscription',
            with: [
                'user' => $this->user,
                'logo' => file_exists($logoPath) ? $logoPath : null
            ]
        );
    }
}
