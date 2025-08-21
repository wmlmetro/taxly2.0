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

  /**
   * @OA\Post(
   *     path="/api/v1/buyer/invoices/{invoice}/accept",
   *     summary="Accept an invoice",
   *     security={{"sanctum":{}}},
   *     tags={"Buyer"},
   *     @OA\Parameter(name="invoice", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice accepted",
   *         @OA\JsonContent(example={
   *             "message": "Invoice accepted",
   *             "success": true,
   *             "data": {
   *                 "id": 1,
   *                 "status": "accepted"
   *             }
   *         })
   *     )
   * )
   */
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

  /**
   * @OA\Post(
   *     path="/api/v1/buyer/invoices/{invoice}/reject",
   *     summary="Reject an invoice",
   *     security={{"sanctum":{}}},
   *     tags={"Buyer"},
   *     @OA\Parameter(name="invoice", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice rejected",
   *         @OA\JsonContent(example={
   *             "message": "Invoice rejected",
   *             "success": true,
   *             "data": {
   *                 "id": 1,
   *                 "status": "rejected"
   *             }
   *         })
   *     )
   * )
   */
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
