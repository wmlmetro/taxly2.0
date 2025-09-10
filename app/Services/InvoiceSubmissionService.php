<?php

namespace App\Services;

use App\Jobs\DispatchWebhook;
use App\Models\Invoice;
use App\Models\Irns;
use App\Models\Submission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class InvoiceSubmissionService
{
  /**
   * Submit invoice to FIRS.
   */
  public function submit(Invoice $invoice, array $options = []): array
  {
    // Track submission
    $sub = Submission::create([
      'invoice_id' => $invoice->id,
      'channel'    => $options['channel'] ?? 'api',
      'status'     => 'pending',
      'attempts'   => 0,
    ]);

    // Build payload from invoice model
    $payload = $invoice->toFirsPayload();
    // $payload = FirsPayloadBuilder::fromInvoice($invoice);

    try {
      // Example: send to FIRS API
      $response = Http::withToken(config('services.firs.token'))
        ->post(config('services.firs.base_url') . '/api/v1/invoice/sign', $payload);
      print_r($response->body()); // For debugging purposes
      if ($response->failed()) {
        $sub->markFailed($response->body());
        return ['error' => 'FIRS submission failed', 'details' => $response->json()];
      }

      $respData = $response->json();

      // Example FIRS response includes txn_id + irn
      $txnId = $respData['txn_id'] ?? Str::uuid()->toString();
      $irnHash = $respData['irn'] ?? hash('sha256', $invoice->id . $txnId);

      $sub->markSuccess($txnId);

      $irn = Irns::updateOrCreate(
        ['invoice_id' => $invoice->id],
        [
          'irn_hash'      => $irnHash,
          'qr_text'       => $respData['qr_text'] ?? "IRN:{$txnId}",
          'qr_image_path' => $respData['qr_image_path'] ?? null,
        ]
      );

      $invoice->markAsSubmitted();

      // Fire org webhooks
      $webhookPayload = [
        'invoice_id' => $invoice->id,
        'status'     => $invoice->status,
        'irn'        => $irn->irn_hash,
        'txn_id'     => $txnId,
      ];

      foreach ($invoice->organization->webhookEndpoints as $endpoint) {
        DispatchWebhook::dispatch($endpoint, 'invoice.submitted', $webhookPayload)
          ->onQueue('webhooks');
      }

      return [
        'submission_id' => $sub->id,
        'txn_id'        => $txnId,
        'firs_response' => $respData,
      ];
    } catch (\Throwable $e) {
      $sub->markFailed($e->getMessage());
      return ['error' => 'Exception during submission', 'details' => $e->getMessage()];
    }
  }
}
