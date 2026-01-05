<?php

namespace App\Jobs;

use App\Models\ExchangeInvoice;
use App\Services\ExchangeInvoiceProcessingService;
use App\Services\TenantResolverService;
use App\Services\FirsAcknowledgementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncExchangeInvoicesJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * The number of times the job may be attempted.
   *
   * @var int
   */
  public $tries = 3;

  /**
   * The number of seconds the job can run before timing out.
   *
   * @var int
   */
  public $timeout = 300; // 5 minutes

  /**
   * The number of seconds to wait before retrying the job.
   *
   * @var int
   */
  public $backoff = 300; // 5 minutes

  /**
   * Create a new job instance.
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(
    ExchangeInvoiceProcessingService $processingService,
    TenantResolverService $tenantResolver,
    FirsAcknowledgementService $acknowledgementService
  ) {
    Log::info('SyncExchangeInvoicesJob started');

    try {
      // Get unassigned invoices that need retry
      $unassignedInvoices = ExchangeInvoice::unassigned()
        ->where('status', ExchangeInvoice::STATUS_TRANSMITTED)
        ->where('created_at', '>=', now()->subDays(7)) // Only last 7 days
        ->get();

      Log::info('Found unassigned invoices for retry', [
        'count' => $unassignedInvoices->count(),
      ]);

      foreach ($unassignedInvoices as $invoice) {
        try {
          // Retry tenant resolution
          $resolutionResult = $tenantResolver->retryResolution($invoice);

          if ($resolutionResult['success']) {
            $invoice->update([
              'tenant_id' => $resolutionResult['tenant_id'],
              'integrator_id' => $resolutionResult['integrator_id'],
            ]);

            Log::info('Tenant resolved for unassigned invoice', [
              'invoice_id' => $invoice->id,
              'tenant_id' => $resolutionResult['tenant_id'],
            ]);

            // Dispatch webhook
            $webhookResult = app(\App\Services\IntegratorWebhookDispatchService::class)
              ->dispatchInvoiceWebhook($invoice);

            if ($webhookResult['success']) {
              // Retry acknowledgement
              $acknowledgementService->retryAcknowledgement($invoice);
            }
          }
        } catch (\Exception $e) {
          Log::error('Failed to process unassigned invoice', [
            'invoice_id' => $invoice->id,
            'error' => $e->getMessage(),
          ]);
        }
      }

      // Pull recent invoices from FIRS that might have been missed
      $this->pullMissedInvoices($processingService);

      Log::info('SyncExchangeInvoicesJob completed successfully');
    } catch (\Exception $e) {
      Log::error('SyncExchangeInvoicesJob failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      throw $e;
    }
  }

  /**
   * Pull missed invoices from FIRS Exchange
   *
   * @param ExchangeInvoiceProcessingService $processingService
   * @return void
   */
  private function pullMissedInvoices(ExchangeInvoiceProcessingService $processingService): void
  {
    Log::info('Pulling missed invoices from FIRS Exchange');

    try {
      // Get recently transmitted invoices from FIRS
      $recentInvoices = app(\App\Services\FirsApiService::class)
        ->pullTransmittedInvoices();

      if (!$recentInvoices || !isset($recentInvoices['data'])) {
        Log::warning('No recent invoices found in FIRS Exchange');
        return;
      }

      $pulledCount = 0;
      $skippedCount = 0;

      foreach ($recentInvoices['data'] as $invoiceData) {
        try {
          $irn = $invoiceData['irn'] ?? null;

          if (!$irn) {
            continue;
          }

          // Check if we already have this invoice
          $existingInvoice = ExchangeInvoice::where('irn', $irn)->first();

          if ($existingInvoice) {
            $skippedCount++;
            continue;
          }

          // Process the missed invoice
          $result = $processingService->pullAndProcessInvoice($irn);

          if ($result['success']) {
            $pulledCount++;
            Log::info('Pulled missed invoice', [
              'irn' => $irn,
              'invoice_id' => $result['invoice_id'],
            ]);
          } else {
            Log::warning('Failed to pull missed invoice', [
              'irn' => $irn,
              'error' => $result['error'],
            ]);
          }
        } catch (\Exception $e) {
          Log::error('Failed to process missed invoice', [
            'invoice_data' => $invoiceData,
            'error' => $e->getMessage(),
          ]);
        }
      }

      Log::info('Missed invoices sync completed', [
        'pulled' => $pulledCount,
        'skipped' => $skippedCount,
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to pull missed invoices', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
    }
  }
}
