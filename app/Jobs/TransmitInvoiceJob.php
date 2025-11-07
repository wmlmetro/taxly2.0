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
        Log::info("🚀 Starting invoice transmission for IRN: {$this->transmission->irn}");

        try {
            $response = $firs->transmitInvoice($this->transmission->irn);

            Log::info("📡 FIRS response for IRN {$this->transmission->irn}: ", $response);

            // Determine status based on FIRS response
            $isSuccess = ($response['code'] ?? 500) === 200;
            $status = $isSuccess ? 'completed' : 'failed';

            // Handle specific FIRS error cases
            if (!$isSuccess && isset($response['error']['details'])) {
                $errorDetails = $response['error']['details'];
                if (str_contains($errorDetails, 'already transmitting or confirmed transmitted')) {
                    Log::warning("⚠️ Invoice {$this->transmission->irn} is already transmitted, treating as success");
                    $status = 'completed'; // Treat as completed since it's already transmitted
                }
            }

            // Update DB status
            $this->transmission->update([
                'status' => $status,
                'response_data' => $response,
            ]);

            Log::info("✅ Invoice transmission {$status} for IRN: {$this->transmission->irn}");

            // Notify webhook if provided
            if ($this->transmission->webhook_url) {
                Log::info("📤 Dispatching webhook notification to: {$this->transmission->webhook_url}");

                $webhookPayload = [
                    'irn' => $this->transmission->irn,
                    'status' => $this->transmission->status,
                    'response' => $response,
                    'message' => $isSuccess ? 'Invoice transmitted successfully' : 'Invoice transmission failed',
                    'timestamp' => now()->toIso8601String(),
                ];

                SendWebhookNotificationJob::dispatch(
                    $this->transmission->webhook_url,
                    $webhookPayload
                )->onQueue('webhooks');

                Log::info("📨 Webhook notification dispatched for IRN: {$this->transmission->irn}");
            } else {
                Log::info("ℹ️ No webhook URL provided for IRN: {$this->transmission->irn}");
            }
        } catch (\Throwable $e) {
            Log::error("❌ Invoice transmission failed for IRN: {$this->transmission->irn}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
