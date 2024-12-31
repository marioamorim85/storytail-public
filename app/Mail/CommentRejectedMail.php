<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CommentRejectedMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $comment;

    public function __construct($user, $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Regarding Your Comment - StoryTail'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comment-rejected',
            with: [
                'user' => $this->user,
                'comment' => $this->comment,
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
