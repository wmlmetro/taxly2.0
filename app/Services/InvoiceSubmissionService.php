<?php

namespace App\Services;

use App\Jobs\DispatchWebhook;
use App\Models\Invoice;
use App\Models\Irns;
use App\Models\Submission;
use Illuminate\Support\Str;

class InvoiceSubmissionService
{
  public function submit(Invoice $invoice, array $options = []): array
  {
    // enqueue submission to external ATRS, here we simulate immediate success
    $sub = Submission::create([
      'invoice_id'  => $invoice->id,
      'channel'     => $options['channel'] ?? 'api',
      'status'      => 'pending',
      'attempts'    => 0,
    ]);

    // Simulate an IRN generation & success (replace with actual integration)
    $txnId = Str::uuid()->toString();
    $sub->markSuccess($txnId);

    $irn = Irns::updateOrCreate(
      ['invoice_id' => $invoice->id],
      [
        'irn_hash'      => hash('sha256', $invoice->id . $txnId),
        'qr_text'       => "IRN:{$txnId}",
        'qr_image_path' => null
      ]
    );

    $invoice->markAsSubmitted();

    // Fire org webhooks
    $payload = [
      'invoice_id' => $invoice->id,
      'status'     => $invoice->status,
      'irn'        => $irn->irn_hash,
      'txn_id'     => $txnId,
    ];

    // Dispatch to every endpoint in this org
    $endpoints = $invoice->organization->webhookEndpoints;
    foreach ($endpoints as $endpoint) {
      DispatchWebhook::dispatch($endpoint, 'invoice.submitted', $payload)->onQueue('webhooks');
    }

    return ['submission_id' => $sub->id, 'txn_id' => $txnId];
  }
}
