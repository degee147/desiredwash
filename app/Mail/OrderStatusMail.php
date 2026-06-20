<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly Order  $order,
        public readonly string $statusTitle,
        public readonly string $statusMessage,
        public readonly string $statusEmoji,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->statusEmoji} Order Update — {$this->statusTitle}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order_status');
    }
}
