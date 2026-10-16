<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppUpdateAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public string $appName;
    public string $appUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email)
    {
        $this->email = $email;
        $this->appName = config('app.name', 'WEMU');
        $this->appUrl = config('app.url', 'https://wemu.online');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎶 ' . $this->appName . ' 2.0 is Here! Update & Rediscover Your Music',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.app-update-announcement',
            with: [
                'email' => $this->email,
                'appName' => $this->appName,
                'appUrl' => $this->appUrl,
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
