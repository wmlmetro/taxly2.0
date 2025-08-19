<?php

use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  \Spatie\Permission\Models\Permission::firstOrCreate([
    'name' => 'create invoices',
    'guard_name' => 'web'
  ]);
  \Spatie\Permission\Models\Permission::firstOrCreate([
    'name' => 'update invoices',
    'guard_name' => 'web'
  ]);

  $this->user->givePermissionTo(['create invoices', 'update invoices']);
  $this->actingAs($this->user);
});

it('creates an invoice', function () {
  $response = $this->postJson('/api/v1/invoices', [
    'buyer_organization_ref' => 'TIN999',
    'total_amount'  => 5000,
    'tax_breakdown' => ['VAT' => 375],
    'vat_treatment' => 'standard',
  ]);

  $response->assertCreated()
    ->assertJsonPath('data.invoice.status', 'draft');
});

it('validates an invoice', function () {
  $invoice = \App\Models\Invoice::factory()->create(['organization_id' => $this->user->organization_id]);

  $response = $this->postJson("/api/v1/invoices/{$invoice->id}/validate", []);
  $response->assertOk()
    ->assertJsonPath('data.invoice.status', 'validated');
});

it('submits an invoice', function () {
  $invoice = \App\Models\Invoice::factory()->create([
    'organization_id' => $this->user->organization_id,
    'status' => 'validated'
  ]);

  $response = $this->postJson("/api/v1/invoices/{$invoice->id}/submit", ['channel' => 'api']);

  $response->assertAccepted()
    ->assertJsonStructure(['data' => ['result' => ['submission_id', 'txn_id']]]);
});
