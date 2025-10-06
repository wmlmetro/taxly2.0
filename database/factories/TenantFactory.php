<?php

namespace Database\Factories;

use App\Models\Landlord;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
  public function definition(): array
  {
    return [
      'landlord_id' => Landlord::factory(),
      'name' => $this->faker->company,
      'email' => $this->faker->unique()->safeEmail,
      'password' => bcrypt('password'), // Default password for testing
      'entity_id' => $this->faker->unique()->uuid,
      'brand' => $this->faker->word,
      'domain' => $this->faker->domainName,
    ];
  }
}
