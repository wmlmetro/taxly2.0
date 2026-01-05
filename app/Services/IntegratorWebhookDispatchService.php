<?php

namespace App\Services;

use App\Models\ExchangeInvoice;
use App\Models\WebhookEndpoint;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntegratorWebhookDispatchService
{
  /**
   * Dispatch webhook notification to integrator
   *
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  public function dispatchInvoiceWebhook(ExchangeInvoice $exchangeInvoice): array
  {
    Log::info('Dispatching invoice webhook to integrator', [
      'invoice_id' => $exchangeInvoice->id,
      'irn' => $exchangeInvoice->irn,
      'integrator_id' => $exchangeInvoice->integrator_id,
    ]);

    try {
      // Get webhook endpoints for the integrator
      $webhookEndpoints = WebhookEndpoint::where('tenant_id', $exchangeInvoice->integrator_id)
        ->where('status', 'active')
        ->where(function ($query) {
          $query->where('events', 'like', '%exchange_invoice%')
            ->orWhere('events', 'like', '%all%')
            ->orWhereNull('events');
        })
        ->get();

      if ($webhookEndpoints->isEmpty()) {
        Log::warning('No active webhook endpoints found for integrator', [
          'integrator_id' => $exchangeInvoice->integrator_id,
        ]);

        return [
          'success' => false,
          'error' => 'No active webhook endpoints found',
        ];
      }

      $payload = $this->buildWebhookPayload($exchangeInvoice);
      $results = [];

      foreach ($webhookEndpoints as $endpoint) {
        $result = $this->sendWebhook($endpoint, $payload, $exchangeInvoice);
        $results[] = $result;
      }

      // Check if any webhook was successful
      $successfulWebhooks = collect($results)->where('success', true);

      if ($successfulWebhooks->isNotEmpty()) {
        // Mark invoice as webhook delivered
        $exchangeInvoice->markAsWebhookDelivered();

        Log::info('Invoice webhook delivered successfully', [
          'invoice_id' => $exchangeInvoice->id,
          'successful_endpoints' => $successfulWebhooks->count(),
        ]);

        return [
          'success' => true,
          'message' => 'Webhook delivered successfully',
          'results' => $results,
        ];
      }

      return [
        'success' => false,
        'error' => 'All webhook deliveries failed',
        'results' => $results,
      ];
    } catch (\Exception $e) {
      Log::error('Webhook dispatch failed', [
        'invoice_id' => $exchangeInvoice->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return [
        'success' => false,
        'error' => 'Webhook dispatch failed: ' . $e->getMessage(),
      ];
    }
  }

  /**
   * Build webhook payload
   *
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  private function buildWebhookPayload(ExchangeInvoice $exchangeInvoice): array
  {
    return [
      'irn' => $exchangeInvoice->irn,
      'direction' => $exchangeInvoice->direction,
      'status' => $exchangeInvoice->status,
      'buyer_tin' => $exchangeInvoice->buyer_tin,
      'seller_tin' => $exchangeInvoice->seller_tin,
      'source' => 'FIRS_EXCHANGE',
      'received_at' => $exchangeInvoice->created_at->toIso8601String(),
      'invoice_data' => $exchangeInvoice->invoice_data,
    ];
  }

  /**
   * Send webhook to specific endpoint
   *
   * @param WebhookEndpoint $endpoint
   * @param array $payload
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  private function sendWebhook(WebhookEndpoint $endpoint, array $payload, ExchangeInvoice $exchangeInvoice): array
  {
    $startTime = microtime(true);

    try {
      Log::info('Sending webhook to endpoint', [
        'endpoint_id' => $endpoint->id,
        'url' => $endpoint->url,
        'invoice_id' => $exchangeInvoice->id,
      ]);

      $response = Http::timeout(30)
        ->withHeaders([
          'Content-Type' => 'application/json',
          'X-Webhook-Source' => 'Taxly',
          'X-Webhook-Event' => 'exchange_invoice.received',
        ])
        ->post($endpoint->url, $payload);

      $duration = microtime(true) - $startTime;
      $statusCode = $response->status();

      // Log webhook attempt
      $webhookLog = WebhookLog::create([
        'webhook_endpoint_id' => $endpoint->id,
        'tenant_id' => $endpoint->tenant_id,
        'event_type' => 'exchange_invoice.received',
        'payload' => $payload,
        'response_status' => $statusCode,
        'response_body' => $response->body(),
        'duration_ms' => round($duration * 1000),
        'success' => $response->successful(),
      ]);

      if ($response->successful()) {
        Log::info('Webhook delivered successfully', [
          'endpoint_id' => $endpoint->id,
          'status_code' => $statusCode,
          'duration_ms' => round($duration * 1000),
        ]);

        return [
          'success' => true,
          'endpoint_id' => $endpoint->id,
          'status_code' => $statusCode,
          'duration_ms' => round($duration * 1000),
        ];
      }

      Log::warning('Webhook delivery failed', [
        'endpoint_id' => $endpoint->id,
        'status_code' => $statusCode,
        'response_body' => $response->body(),
      ]);

      return [
        'success' => false,
        'endpoint_id' => $endpoint->id,
        'status_code' => $statusCode,
        'error' => 'HTTP ' . $statusCode . ': ' . $response->body(),
      ];
    } catch (\Exception $e) {
      $duration = microtime(true) - $startTime;

      // Log failed webhook attempt
      WebhookLog::create([
        'webhook_endpoint_id' => $endpoint->id,
        'tenant_id' => $endpoint->tenant_id,
        'event_type' => 'exchange_invoice.received',
        'payload' => $payload,
        'response_status' => 0,
        'response_body' => $e->getMessage(),
        'duration_ms' => round($duration * 1000),
        'success' => false,
      ]);

      Log::error('Webhook request failed', [
        'endpoint_id' => $endpoint->id,
        'error' => $e->getMessage(),
        'duration_ms' => round($duration * 1000),
      ]);

      return [
        'success' => false,
        'endpoint_id' => $endpoint->id,
        'error' => 'Request failed: ' . $e->getMessage(),
      ];
    }
  }
}
