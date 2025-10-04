<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    use SerializesModels;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $this->invoice]);

        return $this->subject("Invoice #{$this->invoice->id}")
            ->markdown('emails.invoice', ['invoice' => $this->invoice])
            ->attachData($pdf->output(), "invoice-{$this->invoice->id}.pdf");
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $this->invoice]);
        return [$pdf];
    }
}
    // Remove attachments() method for compatibility with older Laravel versions.
