<?php

namespace App\Jobs;

use App\Models\ExchangeInvoice;
use App\Services\ExchangeInvoiceProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PullExchangeInvoiceJob implements ShouldQueue
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
  public $timeout = 120;

  /**
   * The number of seconds to wait before retrying the job.
   *
   * @var int
   */
  public $backoff = 60;

  /**
   * The IRN of the invoice to pull
   *
   * @var string
   */
  protected $irn;

  /**
   * Create a new job instance.
   */
  public function __construct(string $irn)
  {
    $this->irn = $irn;
  }

  /**
   * Execute the job.
   */
  public function handle(ExchangeInvoiceProcessingService $processingService)
  {
    Log::info('PullExchangeInvoiceJob started', [
      'irn' => $this->irn,
      'job_id' => $this->job->getJobId(),
    ]);

    try {
      // Check if invoice already exists to prevent duplicates
      $existingInvoice = ExchangeInvoice::where('irn', $this->irn)->first();

      if ($existingInvoice) {
        Log::info('Invoice already exists, skipping pull', [
          'irn' => $this->irn,
          'invoice_id' => $existingInvoice->id,
        ]);
        return;
      }

      // Process the invoice pull
      $result = $processingService->pullAndProcessInvoice($this->irn);

      if ($result['success']) {
        Log::info('PullExchangeInvoiceJob completed successfully', [
          'irn' => $this->irn,
          'invoice_id' => $result['invoice_id'],
          'tenant_id' => $result['tenant_id'] ?? null,
          'integrator_id' => $result['integrator_id'] ?? null,
        ]);
      } else {
        Log::error('PullExchangeInvoiceJob failed', [
          'irn' => $this->irn,
          'error' => $result['error'],
        ]);

        // Throw exception to trigger retry
        throw new \Exception($result['error']);
      }
    } catch (\Exception $e) {
      Log::error('PullExchangeInvoiceJob exception', [
        'irn' => $this->irn,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      throw $e;
    }
  }

  /**
   * Handle a job failure.
   */
  public function failed(\Throwable $exception)
  {
    Log::error('PullExchangeInvoiceJob failed permanently', [
      'irn' => $this->irn,
      'error' => $exception->getMessage(),
      'attempts' => $this->attempts(),
    ]);

    // Create a failed exchange invoice record for tracking
    ExchangeInvoice::create([
      'irn' => $this->irn,
      'buyer_tin' => 'UNKNOWN',
      'seller_tin' => 'UNKNOWN',
      'direction' => ExchangeInvoice::DIRECTION_INCOMING,
      'status' => ExchangeInvoice::STATUS_FAILED,
      'invoice_data' => [
        'error' => 'Failed to pull invoice from FIRS Exchange',
        'exception' => $exception->getMessage(),
      ],
    ]);
  }
}
