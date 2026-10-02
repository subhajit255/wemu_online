<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserSignupMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to WEMU',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user-signup',
            with: [
                'name' => $this->user->name ?? $this->user->first_name ?? 'Listener',
                'email' => $this->user->email,
                'joined_date' => $this->user->created_at ? $this->user->created_at->format('M d, Y') : now()->format('M d, Y'),
            ],
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
