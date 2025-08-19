<?php

use App\Models\User;
use App\Models\Invoice;
use App\Models\AuditEvent;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
  $this->user = User::factory()->create();
  Permission::firstOrCreate(['name' => 'create invoices', 'guard_name' => 'web']);
  Permission::firstOrCreate(['name' => 'update invoices', 'guard_name' => 'web']);
  $this->user->givePermissionTo(['create invoices', 'update invoices']);
  $this->actingAs($this->user);
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
  $invoice = Invoice::factory()->create(['organization_id' => $this->user->organization_id, 'status' => 'validated']);

  $this->postJson("/api/v1/invoices/{$invoice->id}/submit", ['channel' => 'api'])
    ->assertAccepted();

  $event = AuditEvent::where('entity_ref', "invoice:{$invoice->id}")
    ->where('verb', 'submitted')
    ->first();

  expect($event)->not->toBeNull();
  expect($event->actor_id)->toBe($this->user->id);
});
