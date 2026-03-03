<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUs extends Mailable 
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $messageContent,
    ) {}

    public function build(): static
    {
        $escapedName = e($this->name);
        $escapedEmail = e($this->email);
        $escapedMessage = nl2br(e($this->messageContent));

        return $this
            ->subject('Contact Us Inquiry')
            ->from($this->email, $this->name)
            ->replyTo($this->email, $this->name)
            ->html("
                <h1>New Contact Us Message</h1>
                <p><strong>Name:</strong> {$escapedName}</p>
                <p><strong>Email:</strong> {$escapedEmail}</p>
                <p><strong>Message:</strong></p>
                <p>{$escapedMessage}</p>
            ");
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
