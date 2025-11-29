<?php

use Laravel\Sanctum\Sanctum;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\AuditEvent;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
  app(PermissionRegistrar::class)->forgetCachedPermissions();

  // Create a tenant
  $tenant = Tenant::factory()->create();

  // Create an organization inside this tenant
  $org = $tenant->organizations()->create([
    'tin' => 'TIN123',
    'legal_name' => 'Test Org',
    'address' => '123 Street',
  ]);

  // Create a user inside that org
  $this->user = $org->users()->create([
    'name' => 'Test User',
    'email' => 'user@example.com',
    'password' => bcrypt('password'),
  ]);

  // Give user invoice permissions
  Permission::firstOrCreate(['name' => 'create invoices', 'guard_name' => 'web']);
  Permission::firstOrCreate(['name' => 'update invoices', 'guard_name' => 'web']);
  $this->user->syncPermissions(['create invoices', 'update invoices']);

  // Authenticate for API routes using Sanctum
  Sanctum::actingAs($this->user);
  // Also authenticate for the web guard so Auth::id() is available in observers
  $this->actingAs($this->user, 'web');
});

it('logs audit event when invoice is created', function () {
  $this->postJson('/api/v1/invoices', [
    'buyer_organization_ref' => 'TIN555',
    'total_amount'  => 2000,
    'tax_breakdown' => ['VAT' => 150],
    'vat_treatment' => 'standard',
  ])->assertCreated();

  $event = AuditEvent::first();
  expect($event)->not->toBeNull();
  expect($event->verb)->toBe('created');
  expect($event->actor_id)->toBe($this->user->id);
});

it('logs audit event when invoice is submitted', function () {
  $invoice = Invoice::factory()->create([
    'organization_id' => $this->user->organization_id,
    'status' => 'validated'
  ]);

  // The global fake from Pest.php should handle this, but ensure it calls markAsSubmitted
  $this->postJson("/api/v1/invoices/{$invoice->id}/submit", ['channel' => 'api'])
    ->assertAccepted();

  $event = AuditEvent::where('entity_ref', "invoice:{$invoice->id}")
    ->where('verb', 'submitted')
    ->first();

  expect($event)->not->toBeNull();
  expect($event->actor_id)->toBe($this->user->id);
});
