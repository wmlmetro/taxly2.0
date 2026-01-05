<?php

namespace App\Services;

use App\Models\ExchangeInvoice;
use App\Services\FirsApiService;
use Illuminate\Support\Facades\Log;

class FirsAcknowledgementService
{
  protected $firsApiService;

  public function __construct(FirsApiService $firsApiService)
  {
    $this->firsApiService = $firsApiService;
  }

  /**
   * Acknowledge invoice receipt to FIRS Exchange
   *
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  public function acknowledgeInvoice(ExchangeInvoice $exchangeInvoice): array
  {
    Log::info('Acknowledging invoice to FIRS', [
      'invoice_id' => $exchangeInvoice->id,
      'irn' => $exchangeInvoice->irn,
    ]);

    try {
      // Only acknowledge if invoice is assigned to a tenant and webhook was delivered
      if (!$exchangeInvoice->isAssigned() || !$exchangeInvoice->isWebhookDelivered()) {
        Log::warning('Cannot acknowledge invoice - not ready', [
          'invoice_id' => $exchangeInvoice->id,
          'is_assigned' => $exchangeInvoice->isAssigned(),
          'is_webhook_delivered' => $exchangeInvoice->isWebhookDelivered(),
        ]);

        return [
          'success' => false,
          'error' => 'Invoice not ready for acknowledgement',
        ];
      }

      // Skip if already acknowledged
      if ($exchangeInvoice->isAcknowledged()) {
        Log::info('Invoice already acknowledged', [
          'invoice_id' => $exchangeInvoice->id,
          'irn' => $exchangeInvoice->irn,
        ]);

        return [
          'success' => true,
          'message' => 'Invoice already acknowledged',
        ];
      }

      // Call FIRS API to acknowledge
      $result = $this->firsApiService->acknowledgeInvoiceTransmission($exchangeInvoice->irn);

      if ($result['success']) {
        // Mark invoice as acknowledged
        $exchangeInvoice->markAsAcknowledged();

        Log::info('Invoice acknowledged successfully', [
          'invoice_id' => $exchangeInvoice->id,
          'irn' => $exchangeInvoice->irn,
          'acknowledged_at' => $exchangeInvoice->acknowledged_at,
        ]);

        return [
          'success' => true,
          'message' => 'Invoice acknowledged successfully',
        ];
      }

      Log::error('FIRS acknowledgement failed', [
        'invoice_id' => $exchangeInvoice->id,
        'irn' => $exchangeInvoice->irn,
        'error' => $result['error'] ?? 'Unknown error',
      ]);

      return [
        'success' => false,
        'error' => 'FIRS acknowledgement failed: ' . ($result['error'] ?? 'Unknown error'),
      ];
    } catch (\Exception $e) {
      Log::error('Invoice acknowledgement failed', [
        'invoice_id' => $exchangeInvoice->id,
        'irn' => $exchangeInvoice->irn,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return [
        'success' => false,
        'error' => 'Acknowledgement failed: ' . $e->getMessage(),
      ];
    }
  }

  /**
   * Retry acknowledgement for failed invoices
   *
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  public function retryAcknowledgement(ExchangeInvoice $exchangeInvoice): array
  {
    Log::info('Retrying invoice acknowledgement', [
      'invoice_id' => $exchangeInvoice->id,
      'irn' => $exchangeInvoice->irn,
    ]);

    // Only retry if not already acknowledged
    if ($exchangeInvoice->isAcknowledged()) {
      return [
        'success' => true,
        'message' => 'Invoice already acknowledged',
      ];
    }

    return $this->acknowledgeInvoice($exchangeInvoice);
  }
}
