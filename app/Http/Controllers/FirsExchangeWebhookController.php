<?php

namespace App\Http\Controllers;

use App\Models\ExchangeEvent;
use App\Jobs\PullExchangeInvoiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FirsExchangeWebhookController extends Controller
{
  /**
   * @OA\Post(
   *     path="/api/firs-exchange/webhook",
   *     summary="FIRS Exchange Webhook",
   *     description="Receive webhook notifications from FIRS Exchange for invoice transmission events",
   *     operationId="firsExchangeWebhook",
   *     tags={"Exchange"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"irn", "message"},
   *             @OA\Property(
   *                 property="irn",
   *                 type="string",
   *                 description="Invoice Registration Number",
   *                 example="INV0990-088ED42R-20270920"
   *             ),
   *             @OA\Property(
   *                 property="message",
   *                 type="string",
   *                 description="Transmission status message",
   *                 example="TRANSMITTED",
   *                 enum={"TRANSMITTING", "TRANSMITTED", "ACKNOWLEDGED", "FAILED"}
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Webhook received successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "message": "Webhook received successfully",
   *                 "event_id": 123
   *             }
   *         )
   *     ),
   *     @OA\Response(
   *         response=400,
   *         description="Invalid payload",
   *         @OA\JsonContent(
   *             example={
   *                 "error": "Invalid payload",
   *                 "message": "IRN and message are required"
   *             }
   *         )
   *     )
   * )
   */
  public function handleWebhook(Request $request)
  {
    try {
      // Log raw payload for debugging
      Log::info('FIRS Exchange webhook received', [
        'raw_payload' => $request->all(),
        'headers' => $request->headers->all(),
        'ip' => $request->ip(),
      ]);

      // Validate payload
      $validator = Validator::make($request->all(), [
        'irn' => 'required|string',
        'message' => 'required|string',
      ]);

      if ($validator->fails()) {
        Log::warning('FIRS Exchange webhook validation failed', [
          'errors' => $validator->errors(),
          'payload' => $request->all(),
        ]);

        return response()->json([
          'error' => 'Invalid payload',
          'message' => 'IRN and message are required',
        ], 400);
      }

      $validated = $validator->validated();
      $irn = $validated['irn'];
      $message = strtoupper($validated['message']);

      // Create exchange event
      $exchangeEvent = ExchangeEvent::create([
        'irn' => $irn,
        'status' => $this->mapMessageToStatus($message),
        'raw_payload' => $request->all(),
      ]);

      Log::info('Exchange event created', [
        'event_id' => $exchangeEvent->id,
        'irn' => $irn,
        'status' => $exchangeEvent->status,
      ]);

      // Dispatch job to pull invoice details if it's a transmission event
      if (in_array($message, ['TRANSMITTED', 'TRANSMITTING'])) {
        PullExchangeInvoiceJob::dispatch($irn)->onQueue('exchange-invoices');

        Log::info('PullExchangeInvoiceJob dispatched', [
          'irn' => $irn,
          'event_id' => $exchangeEvent->id,
        ]);
      }

      // Mark event as processed
      $exchangeEvent->markAsProcessed();

      return response()->json([
        'message' => 'Webhook received successfully',
        'event_id' => $exchangeEvent->id,
      ], 200);
    } catch (\Exception $e) {
      Log::error('FIRS Exchange webhook processing failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'payload' => $request->all(),
      ]);

      // Still return 200 to prevent FIRS from retrying immediately
      // The job will handle retries
      return response()->json([
        'message' => 'Webhook received but processing failed',
        'error' => 'Internal processing error',
      ], 200);
    }
  }

  /**
   * Map webhook message to status
   *
   * @param string $message
   * @return string
   */
  private function mapMessageToStatus(string $message): string
  {
    return match (strtoupper($message)) {
      'TRANSMITTING' => ExchangeEvent::STATUS_TRANSMITTING,
      'TRANSMITTED' => ExchangeEvent::STATUS_TRANSMITTED,
      'ACKNOWLEDGED' => ExchangeEvent::STATUS_ACKNOWLEDGED,
      'FAILED' => ExchangeEvent::STATUS_FAILED,
      default => ExchangeEvent::STATUS_TRANSMITTED,
    };
  }
}
