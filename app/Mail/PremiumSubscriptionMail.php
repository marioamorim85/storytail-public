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
        return [];
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.premium-subscription',
            with: [
                'user' => $this->user,
                'logo' => 'https://raw.githubusercontent.com/marioamorim85/storytail-public/master/public/images/logo-storyTail.png'
            ]
        );
    }
}
