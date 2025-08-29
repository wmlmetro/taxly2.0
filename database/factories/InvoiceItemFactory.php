<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
  protected $model = InvoiceItem::class;

  public function definition(): array
  {
    return [
      'invoice_id' => Invoice::factory(),
      'description' => $this->faker->sentence(6),
      'quantity' => $this->faker->numberBetween(1, 50),
      'price' => $this->faker->randomFloat(2, 100, 5000),
      'line_total' => fn(array $attrs) => $attrs['quantity'] * $attrs['price'],

      // Extra FIRS fields
      'hsn_code' => 'HSN-' . $this->faker->numerify('###'),
      'product_category' => $this->faker->randomElement(['Food', 'Electronics', 'Services']),
      'discount_rate' => $this->faker->randomFloat(2, 0, 10),
      'discount_amount' => $this->faker->randomFloat(2, 0, 500),
      'fee_rate' => $this->faker->randomFloat(2, 0, 5),
      'fee_amount' => $this->faker->randomFloat(2, 0, 200),
      'invoiced_quantity' => $this->faker->numberBetween(1, 100),
      'line_extension_amount' => $this->faker->randomFloat(2, 100, 5000),
      'item' => [
        'name' => $this->faker->word(),
        'description' => $this->faker->sentence(),
        'sellers_item_identification' => $this->faker->uuid(),
      ],
      'price_details' => [
        'price_amount' => $this->faker->randomFloat(2, 50, 1000),
        'base_quantity' => $this->faker->numberBetween(1, 10),
        'price_unit' => 'NGN per 1',
      ],
    ];
  }
}
