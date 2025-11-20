<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Organization;
use App\Services\InvoiceValidationService;
use App\Services\InvoiceSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceCrudController extends BaseController
{
  use AuthorizesRequests;

  /**
   * Create a new invoice
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'buyer_organization_ref' => 'required|string',
      'total_amount' => 'required|numeric|min:0',
      'tax_breakdown' => 'required|array',
      'vat_treatment' => 'required|in:standard,zero-rated,exempt',
    ]);

    $user = Auth::user();
    $organization = $user->organization;

    // Create customer if customer_id is not provided
    if (!isset($validated['customer_id'])) {
      $customer = Customer::create([
        'name' => $validated['buyer_organization_ref'],
        'tin' => $validated['buyer_organization_ref'],
        'email' => 'customer@example.com',
        'phone' => '+2340000000000',
        'country' => 'NG',
      ]);
      $validated['customer_id'] = $customer->id;
    }

    $invoice = Invoice::create(array_merge($validated, [
      'organization_id' => $organization->id,
      'status' => 'draft',
    ]));

    return response()->json([
      'data' => [
        'invoice' => $invoice->load('customer', 'organization'),
      ],
      'message' => 'Invoice created successfully',
    ], 201);
  }

  /**
   * Validate an invoice
   */
  public function validateInvoice(Request $request, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    try {
      $validationService = app(InvoiceValidationService::class);
      $validationService->validate($invoice);

      $invoice->markAsValidated();
      return response()->json([
        'data' => [
          'invoice' => $invoice->fresh(),
        ],
        'message' => 'Invoice validated successfully',
      ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'data' => [
          'invoice' => $invoice,
        ],
        'errors' => $e->errors(),
        'message' => 'Invoice validation failed',
      ], 422);
    }
  }

  /**
   * Submit an invoice
   */
  public function submit(Request $request, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    $validated = $request->validate([
      'channel' => 'required|in:api,web,manual',
    ]);

    if ($invoice->status !== 'validated') {
      return response()->json([
        'message' => 'Invoice must be validated before submission',
      ], 422);
    }

    $submissionService = app(InvoiceSubmissionService::class);
    $result = $submissionService->submit($invoice, ['channel' => $validated['channel']]);

    if ($result && isset($result['success']) && $result['success']) {
      return response()->json([
        'data' => [
          'result' => [
            'submission_id' => $result['submission_id'],
            'txn_id' => $result['txn_id'],
          ],
        ],
        'message' => 'Invoice submitted successfully',
      ], 202);
    }

    return response()->json([
      'message' => 'Invoice submission failed',
      'errors' => $result['errors'] ?? ['Submission failed'],
    ], 422);
  }
}
