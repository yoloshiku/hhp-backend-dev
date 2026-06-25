<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use \Illuminate\Mail\Mailables\Address;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Contact form data.
     *
     * @var array
     */
    public array $data;

    /**
     * Create a new message instance.
     *
     * @param array $data Contact form data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Define the email envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'), 
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    $this->data['email'], 
                    $this->data['first_name']
                )
            ],            
            subject: 'New Contact Message',
        );
    }

    /**
     * Define the email content.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }

    /**
     * Define email attachments.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}