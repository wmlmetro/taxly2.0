<?php

namespace Database\Factories;

use App\Models\Org;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
  public function definition(): array
  {
    return [
      'organization_id' => Organization::factory(),
      'buyer_organization_ref' => $this->faker->bothify('TIN####'),
      'total_amount' => $this->faker->numberBetween(1000, 10000),
      'tax_breakdown' => ['VAT' => 100],
      'vat_treatment' => 'standard',
      'status' => 'draft',
    ];
  }
}
