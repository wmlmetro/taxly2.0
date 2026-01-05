<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InvoicePdfController extends Controller
{
    public function download(Invoice $invoice)
    {
        $invoice->load('items');

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        return $pdf->download("invoice-{$invoice->id}.pdf");
    }

    public function sendEmail(Invoice $invoice)
    {
        // TODO: update email to get buyer email
        $recipient = $invoice->buyer_organization_ref . '@example.com'; // Replace with real buyer email field
        Mail::to($recipient)->queue(new InvoiceMail($invoice));

        // Fixed: Use explicit route instead of back() to prevent open redirection
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice emailed successfully!');
    }
}
