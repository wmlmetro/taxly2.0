<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Mail\InvoiceTransmissionMail;
use App\Models\CustomerTransmission;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Mail;
use Throwable;

class WebhookController extends Controller
{
  /**
   * Handle incoming webhook from FIRS and optionally forward it
   * to a client (Integrator) webhook.
   */
  public function handle(Request $request)
  {
    // Step 1: Validate FIRS payload + optional integrator webhook URL
    $data = $request->validate([
      'irn'         => 'required|string',
      'message'     => 'required|string',
      'webhook_url' => 'nullable|url', // optional, supplied by integrator or from DB
    ]);

    Log::info('📩 FIRS Webhook Received', $data);

    // Step 2: Persist webhook log for audit
    $webhookRecord = WebhookEndpoint::create([
      'url'     => $data['webhook_url'] ?? env('APP_URL') . '/api/webhooks/firs',
      'irn'     => $data['irn'],
      'message' => $data['message'],
    ]);

    // Send feedback to the integrator webhook (if provided)
    if (!empty($data['webhook_url'])) {
      try {
        Http::post($data['webhook_url'], [
          'irn' => $data['irn'],
          'message' => $data['message'],
        ]);
        Log::info('Webhook forwarded successfully to ' . $data['webhook_url']);
      } catch (\Exception $e) {
        Log::error('Failed to forward webhook: ' . $e->getMessage());
      }
    }

    // Step 3: Locate invoice transmission data
    $transmissionInfo = CustomerTransmission::where('irn', $data['irn'])->first();

    if (!$transmissionInfo) {
      Log::warning('⚠️ No CustomerTransmission record found for IRN: ' . $data['irn']);
    }

    // Step 4: Notify supplier and customer by email (optional)
    try {
      if ($transmissionInfo?->supplier_email) {
        Mail::to($transmissionInfo->supplier_email)
          ->queue(new InvoiceTransmissionMail($data['irn'], $transmissionInfo->supplier_name));
      }

      if ($transmissionInfo?->customer_email) {
        Mail::to($transmissionInfo->customer_email)
          ->queue(new InvoiceTransmissionMail($data['irn'], $transmissionInfo->customer_name));
      }
    } catch (Throwable $e) {
      Log::error('❌ Failed to send notification emails', ['error' => $e->getMessage()]);
    }

    // Step 5: Forward webhook to integrator (if provided)
    if (!empty($data['webhook_url'])) {
      try {
        Log::info('🚀 Forwarding webhook to integrator', [
          'destination' => $data['webhook_url'],
          'payload' => $data,
        ]);

        $response = Http::timeout(10)
          ->retry(3, 2000) // retry 3 times with 2s delay
          ->post($data['webhook_url'], [
            'irn'     => $data['irn'],
            'message' => $data['message'],
            'source'  => 'taxly', // to identify it came from middleware
          ]);

        Log::info('✅ Forwarded webhook successfully', [
          'status' => $response->status(),
          'body'   => $response->json(),
        ]);

        $webhookRecord->update([
          'forwarded_to' => $data['webhook_url'],
          'forward_status' => $response->successful() ? 'success' : 'failed',
          'response_body' => $response->json(),
        ]);

        return response()->json([
          'status'  => 'forwarded',
          'message' => 'Webhook forwarded successfully',
          'response' => $response->json(),
        ], 200);
      } catch (Throwable $e) {
        Log::error('❌ Failed to forward webhook', [
          'error' => $e->getMessage(),
          'destination' => $data['webhook_url'],
        ]);

        $webhookRecord->update([
          'forwarded_to' => $data['webhook_url'],
          'forward_status' => 'failed',
          'response_body' => ['error' => $e->getMessage()],
        ]);

        return response()->json([
          'status'  => 'error',
          'message' => 'Failed to forward webhook to integrator',
          'error'   => $e->getMessage(),
        ], 500);
      }
    }

    // Step 6: If no forwarding webhook provided, just confirm receipt
    return response()->json([
      'status'  => 'received',
      'message' => 'FIRS webhook received and processed successfully',
    ], 200);
  }
}
