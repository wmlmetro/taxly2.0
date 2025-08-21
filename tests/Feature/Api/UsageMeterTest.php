<?php

use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\UsageMeter;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
  $tenant = Tenant::factory()->create();

  $this->organization = Organization::factory()->create([
    'tenant_id' => $this->tenant->id,
  ]);

  $this->user = User::factory()->create([
    'organization_id' => $this->organization->id,
  ]);

  $this->tenant = $tenant;
  Sanctum::actingAs($this->user);
});

it('increments invoice counter when invoice is created', function () {
  $this->postJson('/api/v1/invoices', [
    'buyer_organization_ref' => 'TIN888',
    'total_amount' => 3500,
    'tax_breakdown' => ['VAT' => 250],
    'vat_treatment' => 'standard',
  ])->assertCreated();

  $usage = UsageMeter::where('tenant_id', $this->tenant->id)->first();
  expect($usage)->not->toBeNull();
  expect($usage->counters['invoice_count'])->toBeGreaterThanOrEqual(1);
});

it('increments submission counter when invoice is submitted', function () {
  $invoice = Invoice::factory()->create([
    'organization_id' => $this->user->organization_id,
    'status' => 'validated'
  ]);

  $this->postJson("/api/v1/invoices/{$invoice->id}/submit", ['channel' => 'api'])
    ->assertAccepted();

  $usage = UsageMeter::where('tenant_id', $this->tenant->id)->first();
  expect($usage)->not->toBeNull();
  expect($usage->counters['submission_count'])->toBeGreaterThanOrEqual(1);
});
