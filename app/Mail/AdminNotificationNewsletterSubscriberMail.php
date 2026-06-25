<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use \Illuminate\Mail\Mailables\Address;

class AdminNotificationNewsletterSubscriberMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Newsletter Subscriber form data.
     *
     * @var array
     */
    public array $data;

    /**
     * Create a new message instance.
     *
     * @param array $data Newsletter Subscriber form data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'), 
                config('mail.from.name')
            ),
            replyTo: [
                new Address($this->data['email'])
            ],            
            subject: 'New Newsletter Subscriber',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-newsletter-subscriber',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
