<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\StoreInvoiceRequest;
use App\Http\Requests\Auth\SubmitInvoiceRequest;
use App\Http\Requests\Auth\ValidateInvoiceRequest;
use App\Models\Invoice;
use App\Models\UsageMeter;
use App\Services\InvoiceSubmissionService;
use App\Services\InvoiceValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends BaseController
{
  use AuthorizesRequests;

  /**
   * @OA\Get(
   *     path="/api/v1/invoices",
   *     summary="List invoices",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Response(response=200, description="List of invoices")
   * )
   */
  public function index(): JsonResponse
  {
    $orgId = Auth::user()->organization_id;
    $invoices = Invoice::where('organization_id', $orgId)
      ->latest()->paginate(20);

    return $this->sendResponse([
      'invoices' => $invoices->items(),
      'pagination' => [
        'current_page' => $invoices->currentPage(),
        'last_page' => $invoices->lastPage(),
        'total' => $invoices->total(),
      ],
    ], 'Invoices retrieved successfully');
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices",
   *     summary="Create a new invoice",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"buyer_organization_ref","total_amount"},
   *             @OA\Property(property="buyer_organization_ref", type="string", example="TIN123"),
   *             @OA\Property(property="total_amount", type="number", example=1500),
   *             @OA\Property(property="tax_breakdown", type="object", example={"VAT":250}),
   *             @OA\Property(property="vat_treatment", type="string", example="standard")
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Invoice created",
   *         @OA\JsonContent(example={
   *             "message": "Invoice created successfully",
   *             "success": true,
   *             "data": {
   *                 "id": 1,
   *                 "buyer_organization_ref": "TIN123",
   *                 "total_amount": 1500,
   *                 "tax_breakdown": {"VAT": 250},
   *                 "status": "draft"
   *             }
   *         })
   *     )
   * )
   */
  public function store(StoreInvoiceRequest $req): JsonResponse
  {
    $this->authorize('create', Invoice::class);

    $data = $req->validated();
    $data['organization_id'] = Auth::user()->organization_id;
    $data['status'] = 'draft';

    $invoice = Invoice::create($data);

    UsageMeter::incrementCounter($req->user()->organization->tenant_id, 'invoice_count');

    return $this->sendResponse([
      'invoice' => $invoice->load('items'),
    ], 'Invoice created successfully', 201);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices/{invoice}/validate",
   *     summary="Validate an invoice",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(name="invoice", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(response=200, description="Invoice validated")
   * )
   */
  public function validateInvoice(ValidateInvoiceRequest $req, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    app(InvoiceValidationService::class)->validate($invoice, $req->validated());
    $invoice->markAsValidated();

    return $this->sendResponse([
      'invoice' => $invoice,
    ], 'Invoice validated successfully');
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices/{invoice}/submit",
   *     summary="Submit an invoice",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(name="invoice", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             @OA\Property(property="channel", type="string", example="api")
   *         )
   *     ),
   *     @OA\Response(
   *         response=202,
   *         description="Invoice submitted",
   *         @OA\JsonContent(example={
   *             "message": "Invoice submission initiated successfully",
   *             "success": true,
   *             "data": {
   *                 "invoice": {
   *                     "id": 1,
   *                     "status": "submitted"
   *                 },
   *                 "result": "Submission queued"
   *             }
   *         })
   *     )
   * )
   */
  public function submit(SubmitInvoiceRequest $req, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    // Build full FIRS payload
    $payload = $invoice->toFirsPayload();
    // Submit to FIRS service
    $result = app(InvoiceSubmissionService::class)->submit($invoice, $payload);

    $invoice->markAsSubmitted();
    UsageMeter::incrementCounter($invoice->organization->tenant_id, 'submission_count');

    return $this->sendResponse([
      'invoice' => $invoice->load('items'),
      'result'  => $result,
    ], 'Invoice submission initiated successfully', 202);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/{invoice}",
   *     summary="Get invoice details",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(name="invoice", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(response=200, description="Invoice details")
   * )
   */
  public function show(Invoice $invoice): JsonResponse
  {
    $this->authorize('view', $invoice);

    return $this->sendResponse(
      $invoice->load(['items', 'irn', 'submissions', 'acceptances', 'artifacts']),
      'Invoice retrieved successfully'
    );
  }
}
