<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Validation\ValidationException;

class InvoiceValidationService
{
  public function validate(Invoice $invoice, array $data = []): void
  {
    // JSON schema or business rules can live here
    if ($invoice->total_amount <= 0) {
      throw ValidationException::withMessages(['total_amount' => 'Total must be greater than 0']);
    }

    // additional VAT/WHT checks here…
  }
}
