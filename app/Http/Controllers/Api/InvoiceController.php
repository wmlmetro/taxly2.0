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

  public function store(StoreInvoiceRequest $req): JsonResponse
  {
    $invoice = Invoice::create([
      'organization_id'         => Auth::user()->organization_id,
      'buyer_organization_ref'  => $req->buyer_organization_ref,
      'total_amount'   => $req->total_amount,
      'tax_breakdown'  => $req->tax_breakdown,
      'vat_treatment'  => $req->vat_treatment,
      'wht_amount'     => $req->wht_amount ?? 0,
      'status'         => 'draft',
    ]);

    UsageMeter::incrementCounter($req->user()->organization->tenant_id, 'invoice_count');

    return $this->sendResponse([
      'invoice' => $invoice,
    ], 'Invoice created successfully', 201);
  }

  public function validateInvoice(ValidateInvoiceRequest $req, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    app(InvoiceValidationService::class)->validate($invoice, $req->validated());
    $invoice->markAsValidated();

    return $this->sendResponse([
      'invoice' => $invoice,
    ], 'Invoice validated successfully');
  }

  public function submit(SubmitInvoiceRequest $req, Invoice $invoice): JsonResponse
  {
    $this->authorize('update', $invoice);

    $result = app(InvoiceSubmissionService::class)->submit($invoice, $req->validated());

    UsageMeter::incrementCounter($invoice->organization->tenant_id, 'submission_count');

    return $this->sendResponse([
      'invoice' => $invoice,
      'result'  => $result,
    ], 'Invoice submission initiated successfully', 202);
  }

  public function show(Invoice $invoice): JsonResponse
  {
    $this->authorize('view', $invoice);

    return $this->sendResponse(
      $invoice->load(['irn', 'submissions', 'acceptances', 'artifacts']),
      'Invoice retrieved successfully'
    );
  }
}
