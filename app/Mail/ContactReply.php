<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public string $replyMessage,
    ) {}

    public function envelope(): Envelope
    {
        $adminAddress = config('mail.admin_address');

        return new Envelope(
            subject: 'Réponse à votre message - Agro Eco BAARA',
            replyTo: $adminAddress ? [new Address($adminAddress, config('app.name'))] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
        );
    }
}
