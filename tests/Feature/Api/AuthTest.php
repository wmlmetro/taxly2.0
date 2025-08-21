<?php

use Illuminate\Support\Facades\Hash;

it('registers a new user and org', function () {
  $response = $this->postJson('/api/v1/auth/register', [
    'tenant_name'  => 'Acme Corp',
    'name'         => 'Admin User',
    'email'        => 'admin@example.com',
    'password'     => 'secret123',
    'password_confirmation' => 'secret123',
    'tin'          => 'TIN123',
    'legal_name'   => 'Acme Corporation',
    'address'      => '123 Main St',
  ]);

  $response->assertCreated()
    ->assertJsonStructure([
      'message',
      'token',
      'user' => [
        'id',
        'name',
        'email',
        'organization_id',
      ]
    ]);
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
