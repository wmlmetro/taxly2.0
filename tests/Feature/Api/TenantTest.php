<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
  $user = User::factory()->create();
  Sanctum::actingAs($user);
});

it('creates a tenant', function () {
  $response = $this->postJson('/api/v1/tenants', [
    'name' => 'Test Tenant',
    'brand' => 'Acme Inc',
    'domain' => 'acme.test',
    'feature_flags' => ['invoices' => true],
    'retention_policy' => '1y',
  ]);

  $response->assertCreated()
    ->assertJsonPath('data.tenant.name', 'Test Tenant');
});
