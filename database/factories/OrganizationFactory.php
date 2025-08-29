<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
  public function definition(): array
  {
    return [
      'tenant_id' => Tenant::factory(),
      'tin' => $this->faker->unique()->numerify('TIN####'),
      'trade_name' => $this->faker->company,
      'street_name' => $this->faker->streetAddress,
    ];
  }
}
