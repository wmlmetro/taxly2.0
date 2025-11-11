<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerTransmission;
use App\Models\WebhookEndpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebhookController extends Controller
{
  /**
   * Handle incoming webhook from FIRS and forward or notify as needed.
   */
  public function handle(Request $request)
  {
    // 1️⃣ Validate incoming payload
    $data = $request->validate([
      'irn'         => 'required|string',
      'message'     => 'required|string',
      'webhook_url' => 'nullable|url',
    ]);

    Log::info('📩 FIRS Webhook Received', $data);

    // 2️⃣ Persist log
    $webhookRecord = WebhookEndpoint::firstOrCreate(
      [
        'irn'     => $data['irn'],
        'message' => $data['message'],
      ],
      [
        'url' => $data['webhook_url'] ?? env('APP_URL') . '/api/webhooks/firs',
      ]
    );

    if (!$webhookRecord->wasRecentlyCreated) {
      Log::info("🔁 Duplicate webhook ignored for IRN {$data['irn']} and message {$data['message']}");
    }

    // 3️⃣ Find invoice transmission info
    $transmission = CustomerTransmission::where('irn', $data['irn'])->first();

    if (!$transmission) {
      Log::warning("⚠️ No CustomerTransmission record found for IRN: {$data['irn']}");
    }

    // 4️⃣ Forward webhook if provided
    if (!empty($data['webhook_url'])) {
      return $this->forwardWebhook($data, $webhookRecord);
    }

    // 5️⃣ If not forwarded, just confirm receipt
    return response()->json([
      'status'  => 'received',
      'message' => 'Webhook received and processed successfully',
    ]);
  }

  /**
   * Forward the webhook to the integrator or external endpoint.
   */
  protected function forwardWebhook(array $data, WebhookEndpoint $record)
  {
    try {
      Log::info('🚀 Forwarding webhook to integrator', [
        'destination' => $data['webhook_url'],
        'payload'     => $data,
      ]);

      $response = Http::timeout(10)
        ->retry(3, 2000)
        ->post($data['webhook_url'], [
          'irn'     => $data['irn'],
          'message' => $data['message'],
          'source'  => 'taxly',
        ]);

      $record->update([
        'forwarded_to'   => $data['webhook_url'],
        'forward_status' => $response->successful() ? 'success' : 'failed',
        'response_body'  => $response->json(),
      ]);

      Log::info("✅ Webhook forwarded successfully to {$data['webhook_url']}", [
        'status' => $response->status(),
      ]);

      return response()->json([
        'status'   => 'forwarded',
        'message'  => 'Webhook forwarded successfully',
        'response' => $response->json(),
      ]);
    } catch (Throwable $e) {
      Log::error('❌ Webhook forwarding failed', [
        'destination' => $data['webhook_url'],
        'error'       => $e->getMessage(),
      ]);

      $record->update([
        'forwarded_to'   => $data['webhook_url'],
        'forward_status' => 'failed',
        'response_body'  => ['error' => $e->getMessage()],
      ]);

      return response()->json([
        'status'  => 'error',
        'message' => 'Failed to forward webhook to integrator',
        'error'   => $e->getMessage(),
      ], 500);
    }
  }
}
