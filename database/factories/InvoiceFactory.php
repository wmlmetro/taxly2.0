<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
  protected $model = Invoice::class;

  public function definition(): array
  {
    return [
      'organization_id' => Organization::factory(),
      'buyer_organization_ref' => 'TIN-' . $this->faker->unique()->numerify('########'),
      'total_amount' => $this->faker->randomFloat(2, 1000, 100000),
      'tax_breakdown' => [
        'VAT' => $this->faker->randomFloat(2, 50, 2000),
        'WHT' => $this->faker->randomFloat(2, 20, 500),
      ],
      'vat_treatment' => $this->faker->randomElement(['standard', 'exempt', 'zero-rated']),
      'wht_amount' => $this->faker->randomFloat(2, 0, 500),
      'status' => 'draft',

      // FIRS schema fields
      'irn' => 'IRN-' . strtoupper($this->faker->bothify('???-#####')),
      'issue_date' => now()->toDateString(),
      'due_date' => now()->addDays(30)->toDateString(),
      'invoice_type_code' => '396',
      'document_currency_code' => 'NGN',
      'tax_currency_code' => 'NGN',
      'note' => $this->faker->sentence(),
      'payment_status' => 'PENDING',
    ];
  }
}
