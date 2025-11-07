<?php

namespace App\Jobs;

use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $webhookUrl;
    protected array $payload;

    public int $tries = 5;

    public function __construct(string $webhookUrl, array $payload)
    {
        $this->webhookUrl = $webhookUrl;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        // Create log entry before attempting delivery
        $log = WebhookLog::create([
            'webhook_url' => $this->webhookUrl,
            'irn' => $this->payload['irn'] ?? null,
            'payload' => $this->payload,
            'status' => 'pending',
        ]);

        Log::info("🚀 Starting webhook delivery to: {$this->webhookUrl}", [
            'irn' => $this->payload['irn'] ?? null,
            'payload' => $this->payload,
        ]);

        try {
            $response = Http::timeout(15)->post($this->webhookUrl, $this->payload);

            $log->update([
                'status' => $response->successful() ? 'success' : 'failed',
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'sent_at' => now(),
            ]);

            if ($response->successful()) {
                Log::info("✅ Webhook successfully delivered to {$this->webhookUrl}", [
                    'status_code' => $response->status(),
                    'irn' => $this->payload['irn'] ?? null,
                ]);
            } else {
                Log::warning("⚠️ Webhook delivery returned non-success status: {$response->status()}", [
                    'url' => $this->webhookUrl,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'irn' => $this->payload['irn'] ?? null,
                ]);

                // Don't throw exception for 4xx errors, just log them
                if ($response->status() >= 400 && $response->status() < 500) {
                    return; // Don't retry client errors
                }

                throw new \Exception("Webhook returned status: {$response->status()}");
            }
        } catch (\Throwable $e) {
            Log::error("❌ Webhook delivery failed: {$e->getMessage()}", [
                'url' => $this->webhookUrl,
                'error' => $e->getMessage(),
                'irn' => $this->payload['irn'] ?? null,
            ]);

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            // Only retry on network/connection errors, not on 4xx errors
            if (str_contains($e->getMessage(), '404') || str_contains($e->getMessage(), '400')) {
                Log::warning("🚫 Not retrying webhook due to client error (4xx)", [
                    'url' => $this->webhookUrl,
                    'error' => $e->getMessage(),
                ]);
                return; // Don't retry client errors
            }

            throw $e;
        }
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }
}
