<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceTransmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $irn;
    public string $name;
    public string $confirmUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $irn, string $name)
    {
        $this->irn = $irn;
        $this->name = $name;
        $this->confirmUrl = url("/api/v1/invoice/transmit/{$irn}");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Transmission Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-transmission',
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
