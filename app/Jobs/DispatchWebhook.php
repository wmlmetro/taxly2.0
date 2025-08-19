<?php

namespace App\Jobs;

use App\Models\WebhookEndpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class DispatchWebhook implements ShouldQueue
{
  use Dispatchable, Queueable;

  public function __construct(
    public WebhookEndpoint $endpoint,
    public string $event,
    public array $payload
  ) {}

  public function handle(): void
  {
    if (!$this->endpoint->isSubscribed($this->event)) return;

    $json = json_encode([
      'event' => $this->event,
      'data'  => $this->payload,
    ]);

    $sig = $this->endpoint->signatureFor($json);

    Http::withHeaders([
      'X-Vendra-Signature' => $sig,
      'Content-Type'       => 'application/json',
    ])->post($this->endpoint->url, $json);
  }
}
