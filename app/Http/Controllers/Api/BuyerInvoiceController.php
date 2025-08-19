<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Acceptance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BuyerInvoiceController extends BaseController
{
  use AuthorizesRequests;

  public function accept(Request $request, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    $acc = Acceptance::create([
      'invoice_id'    => $invoice->id,
      'buyer_response' => 'approved',
      'reason_code'   => null,
      'timestamp'     => now(),
      'actor'         => $request->user()->email ?? 'system',
    ]);

    return $this->sendResponse([
      'acceptance' => $acc,
    ], 'Invoice accepted successfully', 201);
  }

  public function reject(Request $request, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    $acc = Acceptance::create([
      'invoice_id'    => $invoice->id,
      'buyer_response' => 'rejected',
      'reason_code'   => $request->input('reason_code'),
      'timestamp'     => now(),
      'actor'         => $request->user()->email ?? 'system',
    ]);

    return $this->sendResponse([
      'acceptance' => $acc,
    ], 'Invoice rejected successfully', 201);
  }
}
