<?php

use App\Models\User;
use App\Models\Invoice;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
  $this->user = User::factory()->create();
  Permission::firstOrCreate(['name' => 'update invoices', 'guard_name' => 'web']);

  $this->user->givePermissionTo(['update invoices']);
  $this->actingAs($this->user);
  $this->invoice = Invoice::factory()->create(['organization_id' => $this->user->organization_id]);
});

it('accepts an invoice', function () {
  $response = $this->postJson("/api/v1/buyer/invoices/{$this->invoice->id}/accept");

  $response->assertCreated()
    ->assertJsonPath('data.acceptance.buyer_response', 'approved');
});

it('rejects an invoice', function () {
  $response = $this->postJson("/api/v1/buyer/invoices/{$this->invoice->id}/reject", [
    'reason_code' => 'DATA_ERROR'
  ]);

  $response->assertCreated()
    ->assertJsonPath('data.acceptance.buyer_response', 'rejected');
});
