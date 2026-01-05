<?php

namespace App\Services;

use App\Models\ExchangeInvoice;
use App\Models\Tenant;
use App\Models\Organization;
use App\Services\FirsApiService;
use App\Services\TenantResolverService;
use App\Services\IntegratorWebhookDispatchService;
use App\Services\FirsAcknowledgementService;
use Illuminate\Support\Facades\Log;

class ExchangeInvoiceProcessingService
{
  protected $firsApiService;
  protected $tenantResolver;
  protected $webhookDispatcher;
  protected $acknowledgementService;

  public function __construct(
    FirsApiService $firsApiService,
    TenantResolverService $tenantResolver,
    IntegratorWebhookDispatchService $webhookDispatcher,
    FirsAcknowledgementService $acknowledgementService
  ) {
    $this->firsApiService = $firsApiService;
    $this->tenantResolver = $tenantResolver;
    $this->webhookDispatcher = $webhookDispatcher;
    $this->acknowledgementService = $acknowledgementService;
  }

  /**
   * Pull invoice details from FIRS Exchange and process them
   *
   * @param string $irn
   * @return array
   */
  public function pullAndProcessInvoice(string $irn): array
  {
    Log::info('Starting invoice pull and process', ['irn' => $irn]);

    try {
      // Pull invoice details from FIRS Exchange
      $invoiceData = $this->pullInvoiceFromFirs($irn);

      if (!$invoiceData) {
        return [
          'success' => false,
          'error' => 'Failed to pull invoice data from FIRS Exchange',
        ];
      }

      // Create or update exchange invoice record
      $exchangeInvoice = $this->createOrUpdateExchangeInvoice($irn, $invoiceData);

      Log::info('Exchange invoice created/updated', [
        'invoice_id' => $exchangeInvoice->id,
        'irn' => $irn,
        'buyer_tin' => $exchangeInvoice->buyer_tin,
        'seller_tin' => $exchangeInvoice->seller_tin,
      ]);

      // Resolve tenant and integrator
      $resolutionResult = $this->tenantResolver->resolveTenantAndIntegrator($exchangeInvoice);

      if ($resolutionResult['success']) {
        $exchangeInvoice->update([
          'tenant_id' => $resolutionResult['tenant_id'],
          'integrator_id' => $resolutionResult['integrator_id'],
        ]);

        Log::info('Tenant and integrator resolved', [
          'invoice_id' => $exchangeInvoice->id,
          'tenant_id' => $resolutionResult['tenant_id'],
          'integrator_id' => $resolutionResult['integrator_id'],
        ]);

        // Dispatch webhook to integrator
        $this->webhookDispatcher->dispatchInvoiceWebhook($exchangeInvoice);

        // Acknowledge to FIRS if configured
        $this->acknowledgementService->acknowledgeInvoice($exchangeInvoice);
      } else {
        Log::warning('Failed to resolve tenant and integrator', [
          'invoice_id' => $exchangeInvoice->id,
          'error' => $resolutionResult['error'],
        ]);

        // Invoice will be retried later by scheduled job
      }

      return [
        'success' => true,
        'invoice_id' => $exchangeInvoice->id,
        'tenant_id' => $exchangeInvoice->tenant_id,
        'integrator_id' => $exchangeInvoice->integrator_id,
      ];
    } catch (\Exception $e) {
      Log::error('Invoice processing failed', [
        'irn' => $irn,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return [
        'success' => false,
        'error' => $e->getMessage(),
      ];
    }
  }

  /**
   * Pull invoice details from FIRS Exchange API
   *
   * @param string $irn
   * @return array|null
   */
  private function pullInvoiceFromFirs(string $irn): ?array
  {
    try {
      // Try to get invoice using lookup endpoint first
      $invoiceData = $this->firsApiService->getInvoiceTransmissionLookup($irn);

      if (!$invoiceData) {
        // Fallback to pull endpoint if lookup fails
        $invoiceData = $this->firsApiService->getInvoiceTransmissionPull($irn);
      }

      if (!$invoiceData) {
        Log::warning('No invoice data found in FIRS Exchange', ['irn' => $irn]);
        return null;
      }

      Log::info('Invoice data pulled from FIRS', [
        'irn' => $irn,
        'data_keys' => array_keys($invoiceData),
      ]);

      return $invoiceData;
    } catch (\Exception $e) {
      Log::error('Failed to pull invoice from FIRS', [
        'irn' => $irn,
        'error' => $e->getMessage(),
      ]);
      return null;
    }
  }

  /**
   * Create or update exchange invoice record
   *
   * @param string $irn
   * @param array $invoiceData
   * @return ExchangeInvoice
   */
  private function createOrUpdateExchangeInvoice(string $irn, array $invoiceData): ExchangeInvoice
  {
    // Determine direction based on TINs and tenant context
    $direction = $this->determineInvoiceDirection($invoiceData);

    return ExchangeInvoice::updateOrCreate(
      ['irn' => $irn],
      [
        'buyer_tin' => $invoiceData['buyer_tin'] ?? $invoiceData['buyerTin'] ?? 'UNKNOWN',
        'seller_tin' => $invoiceData['seller_tin'] ?? $invoiceData['sellerTin'] ?? 'UNKNOWN',
        'direction' => $direction,
        'status' => ExchangeInvoice::STATUS_TRANSMITTED,
        'invoice_data' => $invoiceData,
      ]
    );
  }

  /**
   * Determine invoice direction based on data
   *
   * @param array $invoiceData
   * @return string
   */
  private function determineInvoiceDirection(array $invoiceData): string
  {
    // Logic to determine if invoice is incoming or outgoing
    // This is a simplified version - adjust based on your business rules

    $buyerTin = $invoiceData['buyer_tin'] ?? $invoiceData['buyerTin'] ?? null;
    $sellerTin = $invoiceData['seller_tin'] ?? $invoiceData['sellerTin'] ?? null;

    if (!$buyerTin || !$sellerTin) {
      return ExchangeInvoice::DIRECTION_INCOMING; // Default to incoming
    }

    // Check if any organization has the buyer TIN (incoming for that tenant)
    $buyerOrganization = Organization::where('tin', $buyerTin)->first();

    if ($buyerOrganization) {
      return ExchangeInvoice::DIRECTION_INCOMING;
    }

    // Check if any organization has the seller TIN (outgoing for that tenant)
    $sellerOrganization = Organization::where('tin', $sellerTin)->first();

    if ($sellerOrganization) {
      return ExchangeInvoice::DIRECTION_OUTGOING;
    }

    // Default to incoming if we can't determine
    return ExchangeInvoice::DIRECTION_INCOMING;
  }
}
