<?php

use Illuminate\Support\Facades\Hash;

it('registers a new user and org', function () {
  $tenant = \App\Models\Tenant::factory()->create();

  $response = $this->postJson('/api/v1/auth/register', [
    'tenant_id'   => $tenant->id,
    'tin'         => 'TIN12345',
    'legal_name'  => 'Acme Ltd',
    'address'     => 'Lagos',
    'name'        => 'Admin User',
    'email'       => 'admin@example.com',
    'password'    => 'secret123'
  ]);

  $response->assertCreated()
    ->assertJsonStructure(['message', 'token', 'user']);
});

it('logs in with valid credentials', function () {
  $org = \App\Models\Organization::factory()->create();
  $user = \App\Models\User::factory()->create([
    'organization_id' => $org->id,
    'email' => 'login@test.com',
    'password' => Hash::make('password123'),
  ]);

  $response = $this->postJson('/api/v1/auth/login', [
    'email' => 'login@test.com',
    'password' => 'password123'
  ]);

  $response->assertOk()
    ->assertJsonStructure(['token', 'user']);
});
