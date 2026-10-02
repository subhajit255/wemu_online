<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionPurchasedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $subscriptionDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $subscriptionDetails)
    {
        $this->user = $user;
        $this->subscriptionDetails = $subscriptionDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Purchase Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription',
            with: [
                'name' => $this->user->name ?? $this->user->first_name ?? 'Listener',
                'plan_name' => $this->subscriptionDetails['plan_name'] ?? 'WEMU Premium',
                'valid_through' => $this->subscriptionDetails['valid_through'] ?? now()->addMonth()->format('F d, Y'),
                'order_ref' => $this->subscriptionDetails['order_ref'] ?? uniqid('wemu_sub_'),
                'order_date' => $this->subscriptionDetails['order_date'] ?? now()->format('M d, Y - h:i A'),
                'plan_price' => $this->subscriptionDetails['plan_price'] ?? '$12.99/mo',
                'total_paid' => $this->subscriptionDetails['total_paid'] ?? '$12.99',
                'plan_description' => $this->subscriptionDetails['plan_description'] ?? 'Unlimited ad-free music, valid for 1 month.',
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
