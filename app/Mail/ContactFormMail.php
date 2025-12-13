<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class ContactFormMail extends Mailable
{
    use SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject']
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            with: [
                'name' => $this->data['name'],
                'email' => $this->data['email'],
                'messageText' => $this->data['message'],
                'logo' => 'https://raw.githubusercontent.com/marioamorim85/storytail-public/master/public/images/logo-storyTail.png'
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
