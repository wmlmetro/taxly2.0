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

        try {
            $response = Http::timeout(15)->post($this->webhookUrl, $this->payload);

            $log->update([
                'status' => $response->successful() ? 'success' : 'failed',
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'sent_at' => now(),
            ]);

            if ($response->failed()) {
                throw new \Exception("Webhook returned status: {$response->status()}");
            }

            Log::info("✅ Webhook successfully delivered to {$this->webhookUrl}");
        } catch (\Throwable $e) {
            Log::error("❌ Webhook delivery failed: {$e->getMessage()}");

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            throw $e;
        }
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }
}
