<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $user)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your WEMU Login Verification Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $roleName = ($this->user->user_type ?? 0) == 3 ? 'Artist' : (($this->user->user_type ?? 0) <= 2 ? 'Admin' : 'User');
        return new Content(
            view: 'emails.login-otp',
            with: [
                'otp' => $this->otp,
                'name' => $this->user->name ?? $this->user->first_name ?? $roleName,
                'roleName' => $roleName,
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
