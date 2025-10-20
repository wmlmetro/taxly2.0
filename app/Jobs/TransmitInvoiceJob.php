<?php

namespace App\Jobs;

use App\Models\InvoiceTransmission;
use App\Services\FirsApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransmitInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public InvoiceTransmission $transmission;

    /**
     * Create a new job instance.
     */
    public function __construct(InvoiceTransmission $transmission)
    {
        $this->transmission = $transmission;
    }

    /**
     * Execute the job.
     */
    public function handle(FirsApiService $firs): void
    {
        Log::info("Starting invoice transmission for IRN: {$this->transmission->irn}");

        try {
            $response = $firs->transmitInvoice($this->transmission->irn);

            // Update DB status
            $this->transmission->update([
                'status' => ($response['code'] ?? 500) === 200 ? 'completed' : 'failed',
                'response_data' => $response,
            ]);

            Log::info("Invoice transmission completed for IRN: {$this->transmission->irn}");

            // Notify webhook if provided
            if ($this->transmission->webhook_url) {
                SendWebhookNotificationJob::dispatch(
                    $this->transmission->webhook_url,
                    [
                        'irn' => $this->transmission->irn,
                        'status' => $this->transmission->status,
                        'response' => $response,
                    ]
                )->onQueue('webhooks');
            }
        } catch (\Throwable $e) {
            Log::error("Invoice transmission failed for IRN: {$this->transmission->irn}", [
                'error' => $e->getMessage(),
            ]);

            $this->transmission->update([
                'status' => 'failed',
                'response_data' => ['error' => $e->getMessage()],
            ]);

            // Let Laravel retry automatically (based on retry settings)
            throw $e;
        }
    }

    /**
     * Number of seconds before retrying a failed job.
     */
    public int $backoff = 30;

    /**
     * Maximum number of attempts.
     */
    public int $tries = 3;
}
