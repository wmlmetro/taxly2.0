<?php

namespace Tests\Feature;

use App\Models\ExchangeEvent;
use App\Models\ExchangeInvoice;
use App\Models\Tenant;
use App\Models\Organization;
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeInvoiceHandlingTest extends TestCase
{
  use RefreshDatabase;

  public function test_firs_exchange_webhook_receives_payload()
  {
    $payload = [
      'irn' => 'INV0990-088ED42R-20270920',
      'message' => 'TRANSMITTED',
    ];

    $response = $this->postJson('/api/firs-exchange/webhook', $payload);

    $response->assertStatus(200)
      ->assertJson([
        'message' => 'Webhook received successfully',
      ]);

    // Verify exchange event was created
    $this->assertDatabaseHas('exchange_events', [
      'irn' => 'INV0990-088ED42R-20270920',
      'status' => 'TRANSMITTED',
    ]);
  }

  public function test_firs_exchange_webhook_validates_required_fields()
  {
    $payload = [
      'message' => 'TRANSMITTED',
      // Missing IRN
    ];

    $response = $this->postJson('/api/firs-exchange/webhook', $payload);

    $response->assertStatus(400)
      ->assertJson([
        'error' => 'Invalid payload',
        'message' => 'IRN and message are required',
      ]);
  }

  public function test_exchange_invoice_model_works()
  {
    $invoice = ExchangeInvoice::create([
      'irn' => 'TEST-IRN-123',
      'buyer_tin' => '123456789',
      'seller_tin' => '987654321',
      'direction' => ExchangeInvoice::DIRECTION_INCOMING,
      'status' => ExchangeInvoice::STATUS_TRANSMITTED,
      'invoice_data' => ['test' => 'data'],
    ]);

    $this->assertDatabaseHas('exchange_invoices', [
      'irn' => 'TEST-IRN-123',
      'buyer_tin' => '123456789',
      'seller_tin' => '987654321',
    ]);

    $this->assertTrue($invoice->isIncoming());
    $this->assertFalse($invoice->isOutgoing());
    $this->assertFalse($invoice->isAcknowledged());
    $this->assertFalse($invoice->isWebhookDelivered());
  }

  public function test_exchange_event_model_works()
  {
    $event = ExchangeEvent::create([
      'irn' => 'TEST-IRN-123',
      'status' => ExchangeEvent::STATUS_TRANSMITTED,
      'raw_payload' => ['test' => 'payload'],
    ]);

    $this->assertDatabaseHas('exchange_events', [
      'irn' => 'TEST-IRN-123',
      'status' => 'TRANSMITTED',
    ]);

    $this->assertFalse($event->isProcessed());

    $event->markAsProcessed();
    $this->assertTrue($event->isProcessed());
  }

  public function test_webhook_payload_structure()
  {
    $invoice = ExchangeInvoice::create([
      'irn' => 'TEST-IRN-123',
      'buyer_tin' => '123456789',
      'seller_tin' => '987654321',
      'direction' => ExchangeInvoice::DIRECTION_INCOMING,
      'status' => ExchangeInvoice::STATUS_TRANSMITTED,
      'invoice_data' => ['test' => 'data'],
      'created_at' => now(),
    ]);

    $service = new \App\Services\IntegratorWebhookDispatchService();

    // Use reflection to test private method
    $reflection = new \ReflectionClass($service);
    $method = $reflection->getMethod('buildWebhookPayload');
    $method->setAccessible(true);

    $payload = $method->invoke($service, $invoice);

    $this->assertEquals('TEST-IRN-123', $payload['irn']);
    $this->assertEquals('INCOMING', $payload['direction']);
    $this->assertEquals('TRANSMITTED', $payload['status']);
    $this->assertEquals('123456789', $payload['buyer_tin']);
    $this->assertEquals('987654321', $payload['seller_tin']);
    $this->assertEquals('FIRS_EXCHANGE', $payload['source']);
    $this->assertArrayHasKey('received_at', $payload);
    $this->assertArrayHasKey('invoice_data', $payload);
  }

  public function test_tenant_resolution_logic()
  {
    $tenant = Tenant::factory()->create();

    $organization = Organization::factory()->create([
      'tenant_id' => $tenant->id,
      'tin' => '123456789',
    ]);

    $invoice = ExchangeInvoice::create([
      'irn' => 'TEST-IRN-123',
      'buyer_tin' => '123456789',
      'seller_tin' => '987654321',
      'direction' => ExchangeInvoice::DIRECTION_INCOMING,
      'status' => ExchangeInvoice::STATUS_TRANSMITTED,
      'invoice_data' => ['test' => 'data'],
    ]);

    $service = new \App\Services\TenantResolverService();
    $result = $service->resolveTenantAndIntegrator($invoice);

    $this->assertTrue($result['success']);
    $this->assertEquals($tenant->id, $result['tenant_id']);
    $this->assertEquals($tenant->id, $result['integrator_id']);
  }
}
