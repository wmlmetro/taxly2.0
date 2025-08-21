<?php

use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
  $org = Organization::factory()->create();
  $this->user = User::factory()->create([
    'organization_id' => $org->id,
  ]);
  Permission::firstOrCreate(['name' => 'create webhooks', 'guard_name' => 'web']);
  $this->user->givePermissionTo('create webhooks');
  $this->actingAs($this->user);
});

it('registers a webhook endpoint', function () {
  $response = $this->postJson('/api/v1/webhooks', [
    'url' => 'https://example.com/hook',
    'secret' => 'supersecretkey123',
    'subscribed_events' => ['invoice.submitted']
  ]);

  $response->assertCreated()
    ->assertJsonPath('data.endpoint.url', 'https://example.com/hook');
});

it('lists webhook endpoints', function () {
  $this->getJson('/api/v1/webhooks')
    ->assertOk()
    ->assertJsonStructure(['data']);
});
