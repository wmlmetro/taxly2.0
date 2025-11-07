<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\SubmitInvoiceRequest;
use App\Http\Requests\Auth\ValidateInvoiceRequest;
use App\Http\Requests\Invoice\ValidateInvoiceUpdateRequest;
use App\Jobs\TransmitInvoiceJob;
use App\Models\CustomerTransmission;
use App\Models\InvoiceTransmission;
use App\Services\FirsApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends BaseController
{
  use AuthorizesRequests;

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/search/{business_id}",
   *     summary="Search for invoices from FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="business_id",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Invoice created",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "items": {
   *                         {
   *                             "irn": "INV000002-94019CE5-20250911",
   *                             "payment_status": "PAID",
   *                             "entry_status": "TRANSMITTING",
   *                             "invoice_type_code": "396",
   *                             "issue_date": "2025-09-10T00:00:00Z",
   *                             "issue_time": "17:59:04",
   *                             "due_date": "2025-09-17T00:00:00Z",
   *                             "sync_date": "2025-09-11",
   *                             "document_currency_code": "NGN",
   *                             "tax_currency_code": "NGN"
   *                         }
   *                     },
   *                     "page": {
   *                         "page": 1,
   *                         "size": 10,
   *                         "hasNextPage": false,
   *                         "hasPreviousPage": false,
   *                         "totalCount": 6
   *                     },
   *                     "attributes": null
   *                 },
   *                 "message": "Invoices loaded successfully"
   *             }
   *         )
   *     )
   * )
   */
  public function search(string $business_id): JsonResponse
  {
    $firs = app(FirsApiService::class);

    $response = $firs->searchInvoice($business_id);

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to retrieve invoices from FIRS', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Invoices loaded successfully";
    return response()->json($response);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices/irn/validate",
   *     summary="Validate an invoice IRN on FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"business_id", "invoice_reference", "irn"},
   *             @OA\Property(property="business_id", type="string", example="414a50c3-9ce5-49ec-9ccb-37c28f7cf6be", description="Business ID"),
   *             @OA\Property(property="invoice_reference", type="string", example="INV000002", description="Invoice reference"),
   *             @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911", description="Invoice Registration Number"),
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="IRN validated successfully",
   *         @OA\JsonContent(example={
   *              "code": 200,
   *              "data": {
   *                   "ok": true
   *              },
   *              "message": "IRN validated successfully"
   *         })
   *     )
   * )
   */
  public function validateInvoiceIRN(ValidateInvoiceRequest $req): JsonResponse
  {
    $firs = app(FirsApiService::class);

    $validateIRN = $firs->validateIrn($req->invoice_reference, $req->business_id, $req->irn);
    if (($validateIRN['code'] ?? 500) != 200) {
      return $this->sendError(
        'FIRS IRN validation failed',
        $validateIRN,
        $validateIRN['code'] ?? 422
      );
    }

    $response["message"] = "IRN validated successfully";
    return response()->json($response);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices/validate",
   *     summary="Validate an invoice on FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"channel", "business_id", "invoice_reference", "irn", "issue_date", "invoice_type_code", "document_currency_code", "accounting_supplier_party", "accounting_customer_party", "legal_monetary_total", "invoice_line"},
   *             @OA\Property(property="channel", type="string", example="api", description="Submission channel"),
   *             @OA\Property(property="business_id", type="string", example="414a50c3-9ce5-49ec-9ccb-37c28f7cf6be", description="Business ID"),
   *             @OA\Property(property="invoice_reference", type="string", example="INV000002", description="Invoice reference"),
   *             @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911", description="Invoice Registration Number"),
   *             @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10", description="Issue date"),
   *             @OA\Property(property="due_date", type="string", format="date", example="2025-09-17", description="Due date (optional)"),
   *             @OA\Property(property="issue_time", type="string", format="time", example="17:59:04", description="Issue time (optional)"),
   *             @OA\Property(property="invoice_type_code", type="string", example="396", description="Invoice type code"),
   *             @OA\Property(property="payment_status", type="string", example="PENDING", description="Payment status (optional, defaults to pending)"),
   *             @OA\Property(property="note", type="string", example="dummy_note (will be encryted in storage)", description="Note (optional, will be encrypted)"),
   *             @OA\Property(property="tax_point_date", type="string", format="date", example="2025-09-10", description="Tax point date (optional)"),
   *             @OA\Property(property="document_currency_code", type="string", example="NGN", description="Document currency code"),
   *             @OA\Property(property="tax_currency_code", type="string", example="NGN", description="Tax currency code (optional)"),
   *             @OA\Property(property="accounting_cost", type="string", example="2000", description="Accounting cost (optional)"),
   *             @OA\Property(property="buyer_reference", type="string", example="buyer REF IRN?", description="Buyer reference (optional)"),
   *             @OA\Property(property="invoice_delivery_period", type="object", description="Invoice delivery period (optional)",
   *                 @OA\Property(property="start_date", type="string", format="date", example="2025-09-17"),
   *                 @OA\Property(property="end_date", type="string", format="date", example="2025-09-21")
   *             ),
   *             @OA\Property(property="order_reference", type="string", example="order REF IRN?", description="Order reference (optional)"),
   *             @OA\Property(property="billing_reference", type="array", description="Billing reference (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                     @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *                 )
   *             ),
   *             @OA\Property(property="dispatch_document_reference", type="object", description="Dispatch document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="receipt_document_reference", type="object", description="Receipt document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="originator_document_reference", type="object", description="Originator document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="contract_document_reference", type="object", description="Contract document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="_document_reference", type="array", description="Document reference (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                     @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *                 )
   *             ),
   *             @OA\Property(property="accounting_supplier_party", type="object", description="Accounting supplier party",
   *                 @OA\Property(property="party_name", type="string", example="Aliquam aspernatur"),
   *                 @OA\Property(property="tin", type="string", example="01122228-7187"),
   *                 @OA\Property(property="email", type="string", example="damolaabolarin1@gmail.com"),
   *                 @OA\Property(property="telephone", type="string", example="+2348187136111", description="Must start with + (country code)"),
   *                 @OA\Property(property="business_description", type="string", example="this entity is into sales of Inverter gadgets and installation."),
   *                 @OA\Property(property="postal_address", type="object",
   *                     @OA\Property(property="street_name", type="string", example="123 Main St"),
   *                     @OA\Property(property="city_name", type="string", example="Metropolis"),
   *                     @OA\Property(property="postal_zone", type="string", example="12345"),
   *                     @OA\Property(property="country", type="string", example="NG")
   *                 )
   *             ),
   *             @OA\Property(property="accounting_customer_party", type="object", description="Accounting customer party",
   *                 @OA\Property(property="party_name", type="string", example="Westmetro"),
   *                 @OA\Property(property="tin", type="string", example="17883307-0001"),
   *                 @OA\Property(property="email", type="string", example="business@email.com"),
   *                 @OA\Property(property="telephone", type="string", example="+23480254000000", description="Must start with + (country code)"),
   *                 @OA\Property(property="business_description", type="string", example="this entity is into sales of Cement and building materials"),
   *                 @OA\Property(property="postal_address", type="object",
   *                     @OA\Property(property="street_name", type="string", example="32, owonikoko street"),
   *                     @OA\Property(property="city_name", type="string", example="Gwarikpa"),
   *                     @OA\Property(property="postal_zone", type="string", example="023401"),
   *                     @OA\Property(property="country", type="string", example="NG")
   *                 )
   *             ),
   *             @OA\Property(property="actual_delivery_date", type="string", format="date", example="2025-09-10", description="Actual delivery date (optional)"),
   *             @OA\Property(property="payment_means", type="array", description="Payment means (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="payment_means_code", type="string", example="10"),
   *                     @OA\Property(property="payment_due_date", type="string", format="date", example="2025-09-10")
   *                 )
   *             ),
   *             @OA\Property(property="payment_terms_note", type="string", example="dummy payment terms note (will be encryted in storage)", description="Payment terms note (optional, will be encrypted)"),
   *             @OA\Property(property="allowance_charge", type="array", description="Allowance/charge (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="charge_indicator", type="boolean", example=true, description="true=charge, false=allowance"),
   *                     @OA\Property(property="amount", type="number", format="float", example=1465230.0)
   *                 )
   *             ),
   *             @OA\Property(property="tax_total", type="array", description="Tax total (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="tax_amount", type="number", format="float", example=56.07),
   *                     @OA\Property(property="tax_subtotal", type="array",
   *                         @OA\Items(type="object",
   *                             @OA\Property(property="taxable_amount", type="number", format="float", example=1200000),
   *                             @OA\Property(property="tax_amount", type="number", format="float", example=8),
   *                             @OA\Property(property="tax_category", type="object",
   *                                 @OA\Property(property="id", type="string", example="LOCAL_SALES_TAX"),
   *                                 @OA\Property(property="percent", type="number", format="float", example=7.5)
   *                             )
   *                         )
   *                     )
   *                 )
   *             ),
   *             @OA\Property(property="legal_monetary_total", type="object", description="Legal monetary total (required)",
   *                 @OA\Property(property="tax_exclusive_amount", type="number", format="float", example=1460000),
   *                 @OA\Property(property="tax_inclusive_amount", type="number", format="float", example=1465230),
   *                 @OA\Property(property="line_extension_amount", type="number", format="float", example=1460000),
   *                 @OA\Property(property="payable_amount", type="number", format="float", example=1465230)
   *             ),
   *             @OA\Property(property="invoice_line", type="array", description="Invoice lines (required)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="hsn_code", type="string", example="WM-001"),
   *                     @OA\Property(property="product_category", type="string", example="Inverter"),
   *                     @OA\Property(property="discount_rate", type="number", format="float", example=0),
   *                     @OA\Property(property="discount_amount", type="number", format="float", example=0),
   *                     @OA\Property(property="fee_rate", type="number", format="float", example=1.01),
   *                     @OA\Property(property="fee_amount", type="number", format="float", example=50),
   *                     @OA\Property(property="invoiced_quantity", type="integer", example=5),
   *                     @OA\Property(property="line_extension_amount", type="number", format="float", example=1465230),
   *                     @OA\Property(property="item", type="object",
   *                         @OA\Property(property="name", type="string", example="Solar invert "),
   *                         @OA\Property(property="description", type="string", example="item description"),
   *                         @OA\Property(property="sellers_item_identification", type="string", example="identified as spoon by the seller")
   *                     ),
   *                     @OA\Property(property="price", type="object",
   *                         @OA\Property(property="price_amount", type="number", format="float", example=1465230),
   *                         @OA\Property(property="base_quantity", type="integer", example=1),
   *                         @OA\Property(property="price_unit", type="string", example="NGN per 1")
   *                     )
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice submitted successfully",
   *         @OA\JsonContent(example={
   *              "code": 200,
   *              "data": {
   *                   "ok": true
   *              },
   *              "message": "Invoice signed successfully"
   *         })
   *     )
   * )
   */
  public function validateInvoice(SubmitInvoiceRequest $req): JsonResponse
  {
    $firs = app(FirsApiService::class);

    $validateInvoice = $firs->validateInvoice($req->all());
    if (($validateInvoice['code'] ?? 500) != 200) {
      return $this->sendError(
        'FIRS Invoice validation failed',
        $validateInvoice,
        $validateInvoice['code'] ?? 422
      );
    }

    $validateInvoice["message"] = "Invoice validated successfully";
    return response()->json($validateInvoice);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices/submit",
   *     summary="Submit an invoice to FIRS",
   *     security={{"sanctum":{}}},
   *     description="Calling the submit invoice endpoint, it will first of all validate the IRN, then validate the Invoice and finally submit the invoice",
   *     tags={"Invoices"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"channel", "business_id", "invoice_reference", "irn", "issue_date", "invoice_type_code", "document_currency_code", "accounting_supplier_party", "accounting_customer_party", "legal_monetary_total", "invoice_line"},
   *             @OA\Property(property="channel", type="string", example="api", description="Submission channel"),
   *             @OA\Property(property="business_id", type="string", example="414a50c3-9ce5-49ec-9ccb-37c28f7cf6be", description="Business ID"),
   *             @OA\Property(property="invoice_reference", type="string", example="INV000002", description="Invoice reference"),
   *             @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911", description="Invoice Registration Number"),
   *             @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10", description="Issue date"),
   *             @OA\Property(property="due_date", type="string", format="date", example="2025-09-17", description="Due date (optional)"),
   *             @OA\Property(property="issue_time", type="string", format="time", example="17:59:04", description="Issue time (optional)"),
   *             @OA\Property(property="invoice_type_code", type="string", example="396", description="Invoice type code"),
   *             @OA\Property(property="payment_status", type="string", example="PENDING", description="Payment status (optional, defaults to pending)"),
   *             @OA\Property(property="note", type="string", example="dummy_note (will be encryted in storage)", description="Note (optional, will be encrypted)"),
   *             @OA\Property(property="tax_point_date", type="string", format="date", example="2025-09-10", description="Tax point date (optional)"),
   *             @OA\Property(property="document_currency_code", type="string", example="NGN", description="Document currency code"),
   *             @OA\Property(property="tax_currency_code", type="string", example="NGN", description="Tax currency code (optional)"),
   *             @OA\Property(property="accounting_cost", type="string", example="2000", description="Accounting cost (optional)"),
   *             @OA\Property(property="buyer_reference", type="string", example="buyer REF IRN?", description="Buyer reference (optional)"),
   *             @OA\Property(property="invoice_delivery_period", type="object", description="Invoice delivery period (optional)",
   *                 @OA\Property(property="start_date", type="string", format="date", example="2025-09-17"),
   *                 @OA\Property(property="end_date", type="string", format="date", example="2025-09-21")
   *             ),
   *             @OA\Property(property="order_reference", type="string", example="order REF IRN?", description="Order reference (optional)"),
   *             @OA\Property(property="billing_reference", type="array", description="Billing reference (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                     @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *                 )
   *             ),
   *             @OA\Property(property="dispatch_document_reference", type="object", description="Dispatch document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="receipt_document_reference", type="object", description="Receipt document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="originator_document_reference", type="object", description="Originator document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="contract_document_reference", type="object", description="Contract document reference (optional)",
   *                 @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                 @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *             ),
   *             @OA\Property(property="_document_reference", type="array", description="Document reference (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="irn", type="string", example="INV000002-94019CE5-20250911"),
   *                     @OA\Property(property="issue_date", type="string", format="date", example="2025-09-10")
   *                 )
   *             ),
   *             @OA\Property(property="accounting_supplier_party", type="object", description="Accounting supplier party",
   *                 @OA\Property(property="party_name", type="string", example="Aliquam aspernatur"),
   *                 @OA\Property(property="tin", type="string", example="01122228-7187"),
   *                 @OA\Property(property="email", type="string", example="damolaabolarin1@gmail.com"),
   *                 @OA\Property(property="telephone", type="string", example="+2348187136111", description="Must start with + (country code)"),
   *                 @OA\Property(property="business_description", type="string", example="this entity is into sales of Inverter gadgets and installation."),
   *                 @OA\Property(property="postal_address", type="object",
   *                     @OA\Property(property="street_name", type="string", example="123 Main St"),
   *                     @OA\Property(property="city_name", type="string", example="Metropolis"),
   *                     @OA\Property(property="postal_zone", type="string", example="12345"),
   *                     @OA\Property(property="country", type="string", example="NG")
   *                 )
   *             ),
   *             @OA\Property(property="accounting_customer_party", type="object", description="Accounting customer party",
   *                 @OA\Property(property="party_name", type="string", example="Westmetro"),
   *                 @OA\Property(property="tin", type="string", example="17883307-0001"),
   *                 @OA\Property(property="email", type="string", example="business@email.com"),
   *                 @OA\Property(property="telephone", type="string", example="+23480254000000", description="Must start with + (country code)"),
   *                 @OA\Property(property="business_description", type="string", example="this entity is into sales of Cement and building materials"),
   *                 @OA\Property(property="postal_address", type="object",
   *                     @OA\Property(property="street_name", type="string", example="32, owonikoko street"),
   *                     @OA\Property(property="city_name", type="string", example="Gwarikpa"),
   *                     @OA\Property(property="postal_zone", type="string", example="023401"),
   *                     @OA\Property(property="country", type="string", example="NG")
   *                 )
   *             ),
   *             @OA\Property(property="actual_delivery_date", type="string", format="date", example="2025-09-10", description="Actual delivery date (optional)"),
   *             @OA\Property(property="payment_means", type="array", description="Payment means (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="payment_means_code", type="string", example="10"),
   *                     @OA\Property(property="payment_due_date", type="string", format="date", example="2025-09-10")
   *                 )
   *             ),
   *             @OA\Property(property="payment_terms_note", type="string", example="dummy payment terms note (will be encryted in storage)", description="Payment terms note (optional, will be encrypted)"),
   *             @OA\Property(property="allowance_charge", type="array", description="Allowance/charge (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="charge_indicator", type="boolean", example=true, description="true=charge, false=allowance"),
   *                     @OA\Property(property="amount", type="number", format="float", example=1465230.0)
   *                 )
   *             ),
   *             @OA\Property(property="tax_total", type="array", description="Tax total (optional)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="tax_amount", type="number", format="float", example=56.07),
   *                     @OA\Property(property="tax_subtotal", type="array",
   *                         @OA\Items(type="object",
   *                             @OA\Property(property="taxable_amount", type="number", format="float", example=1200000),
   *                             @OA\Property(property="tax_amount", type="number", format="float", example=8),
   *                             @OA\Property(property="tax_category", type="object",
   *                                 @OA\Property(property="id", type="string", example="LOCAL_SALES_TAX"),
   *                                 @OA\Property(property="percent", type="number", format="float", example=7.5)
   *                             )
   *                         )
   *                     )
   *                 )
   *             ),
   *             @OA\Property(property="legal_monetary_total", type="object", description="Legal monetary total (required)",
   *                 @OA\Property(property="tax_exclusive_amount", type="number", format="float", example=1460000),
   *                 @OA\Property(property="tax_inclusive_amount", type="number", format="float", example=1465230),
   *                 @OA\Property(property="line_extension_amount", type="number", format="float", example=1460000),
   *                 @OA\Property(property="payable_amount", type="number", format="float", example=1465230)
   *             ),
   *             @OA\Property(property="invoice_line", type="array", description="Invoice lines (required)",
   *                 @OA\Items(type="object",
   *                     @OA\Property(property="hsn_code", type="string", example="WM-001"),
   *                     @OA\Property(property="product_category", type="string", example="Inverter"),
   *                     @OA\Property(property="discount_rate", type="number", format="float", example=0),
   *                     @OA\Property(property="discount_amount", type="number", format="float", example=0),
   *                     @OA\Property(property="fee_rate", type="number", format="float", example=1.01),
   *                     @OA\Property(property="fee_amount", type="number", format="float", example=50),
   *                     @OA\Property(property="invoiced_quantity", type="integer", example=5),
   *                     @OA\Property(property="line_extension_amount", type="number", format="float", example=1465230),
   *                     @OA\Property(property="item", type="object",
   *                         @OA\Property(property="name", type="string", example="Solar invert "),
   *                         @OA\Property(property="description", type="string", example="item description"),
   *                         @OA\Property(property="sellers_item_identification", type="string", example="identified as spoon by the seller")
   *                     ),
   *                     @OA\Property(property="price", type="object",
   *                         @OA\Property(property="price_amount", type="number", format="float", example=1465230),
   *                         @OA\Property(property="base_quantity", type="integer", example=1),
   *                         @OA\Property(property="price_unit", type="string", example="NGN per 1")
   *                     )
   *                 )
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice submitted successfully",
   *         @OA\JsonContent(example={
   *              "code": 201,
   *              "data": {
   *                   "ok": true
   *              },
   *              "message": "Invoice signed successfully"
   *         })
   *     )
   * )
   */
  public function submit(SubmitInvoiceRequest $req): JsonResponse
  {
    $firs = app(FirsApiService::class);

    // Step 1. Validate IRN
    $validateIRN = $firs->validateIrn($req->invoice_reference, $req->business_id, $req->irn);
    if (($validateIRN['code'] ?? 500) != 200) {
      return $this->sendError(
        'FIRS IRN validation failed',
        $validateIRN,
        $validateIRN['code'] ?? 422
      );
    }

    // Step 2. Validate Invoice structure
    $validateInvoice = $firs->validateInvoice($req->all());
    if (($validateInvoice['code'] ?? 500) != 200) {
      return $this->sendError(
        'FIRS Invoice validation failed',
        $validateInvoice,
        $validateInvoice['code'] ?? 422
      );
    }

    // Step 3. Sign invoice
    $signedInvoice = $firs->invoiceSigning($req->all());
    if (($signedInvoice['code'] ?? 500) != 201) {
      return $this->sendError(
        'FIRS Invoice signing failed',
        $signedInvoice,
        $signedInvoice['code'] ?? 422
      );
    }

    // Step 4. Store Supplier and Customer info
    CustomerTransmission::create([
      'irn' => $req->irn,
      'supplier_name' => $req->accounting_supplier_party['party_name'] ?? null,
      'supplier_email' => $req->accounting_supplier_party['email'] ?? null,
      'customer_name' => $req->accounting_customer_party['party_name'] ?? null,
      'customer_email' => $req->accounting_customer_party['email'] ?? null,
    ]);

    // Step 5. Success
    $signedInvoice["message"] = "Invoice submission initiated successfully";
    return response()->json($signedInvoice);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/transmit/health-check",
   *     summary="Check the health of the FIRS transmission service",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Response(
   *         response=200,
   *         description="Health check succeeded",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "ok": true,
   *                     "item": {} 
   *                 },
   *                 "message": "Health check succeeded."
   *             }
   *         )
   *     )
   * )
   */
  public function healthCheck()
  {
    $firs = app(FirsApiService::class);

    $response = $firs->selfHealthCheck();

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to check transmission health ', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Health check succeeded.";
    return response()->json($response);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/transmit/{IRN}/lookup",
   *     summary="Retrieves details about the invoice and the involved parties.",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="IRN",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Transmission parties loaded successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "accounting_supplier_party": {},
   *                     "accounting_customer_party": {} 
   *                 },
   *                 "message": "Transmission parties loaded successfully."
   *             }
   *         )
   *     )
   * )
   */
  public function getInvoiceTransmitted(string $irn)
  {
    $firs = app(FirsApiService::class);

    $response = $firs->getTransmittingInvoice($irn);

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to load transmitted parties', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Transmission parties loaded successfully.";
    return response()->json($response);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/transmit/tin/{tin}/lookup",
   *     summary="Retrieves details about the invoice and the involved parties.",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="tin",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Transmission parties loaded successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "accounting_supplier_party": {},
   *                     "accounting_customer_party": {} 
   *                 },
   *                 "message": "Transmission parties loaded successfully."
   *             }
   *         )
   *     )
   * )
   */
  public function getInvoiceTransmittedByTin(string $tin)
  {
    $firs = app(FirsApiService::class);

    $response = $firs->getTransmittedInvoiceByTin($tin);

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to load transmitted parties', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Transmission parties loaded successfully.";
    return response()->json($response);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/invoices/{irn}/transmit",
   *     summary="Transmit an invoice using IRN on FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="irn",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\RequestBody(
   *         required=false,
   *         @OA\JsonContent(
   *             @OA\Property(
   *                 property="webhook_url",
   *                 type="string",
   *                 example="https://example.com/webhook"
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice transmission",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "ok": true
   *                 },
   *                 "message": "Invoice transmission started."
   *             }
   *         )
   *     )
   * )
   */
  public function transmit(Request $request, $irn)
  {
    $validated = $request->validate([
      'webhook_url' => 'nullable|url',
    ]);

    $transmission = InvoiceTransmission::create([
      'irn' => $irn,
      'webhook_url' => $validated['webhook_url'] ?? null,
      'status' => 'pending',
    ]);

    // Dispatch background job
    TransmitInvoiceJob::dispatch($transmission)->onQueue('invoices');
    Log::info('Dispatched TransmitInvoiceJob for IRN: ' . $irn);
    Log::info('Webhook URL: ' . ($transmission->webhook_url ?? 'None'));
    return response()->json([
      'code' => 200,
      'data' => ['ok' => true],
      'message' => 'Invoice transmission queued successfully.',
      'irn' => $irn,
      'webhook_url' => $transmission->webhook_url,
    ]);
  }

  /**
   * @OA\Patch(
   *     path="/api/v1/invoices/{irn}/update",
   *     summary="Update an invoice using IRN on FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="irn",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"payment_status"},
   *             @OA\Property(
   *                 property="payment_status",
   *                 type="string",
   *                 example="REJECTED",
   *                 description="PENDING, PAID, REJECTED"
   *             ),
   *             @OA\Property(
   *                 property="reference",
   *                 type="string",
   *                 example="payment_reference_or_note",
   *                 description="Payment reference or note"
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice updated successfully in FIRS",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "ok": true
   *                 },
   *                 "message": "Invoice updated successfully in FIRS"
   *             }
   *         )
   *     )
   * )
   */
  public function update(ValidateInvoiceUpdateRequest $req, string $irn): JsonResponse
  {
    $firs = app(FirsApiService::class);

    $response = $firs->updateInvoice($irn, $req->all());

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to update invoice in FIRS', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Invoice updated successfully in FIRS";
    return response()->json($response);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/{irn}/confirm",
   *     summary="Confirm an invoice using IRN on FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="irn",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice confirmed successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "issue_date": "2025-09-10",
   *                     "due_date": "2025-09-17",
   *                     "sync_date": "2025-09-11",
   *                     "payment_status": "PAID",
   *                     "transmitted": false,
   *                     "delivered": false
   *                 },
   *                 "message": "Invoice confirmation state from FIRS"
   *             }
   *         )
   *     )
   * )
   */
  public function confirm(string $irn): JsonResponse
  {
    $firs = app(FirsApiService::class);

    $response = $firs->confirmInvoice($irn);

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to confirm invoice in FIRS', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Invoice confirmation state from FIRS";
    return response()->json($response);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/transmit/pull",
   *     summary="List invoices transmitted to FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Response(
   *         response=200,
   *         description="Transmitted Invoices pulled successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "ok": true,
   *                     "item": {} 
   *                 },
   *                 "message": "Transmitted Invoices pulled successfully."
   *             }
   *         )
   *     )
   * )
   */
  public function pullTransmittedInvoices()
  {
    $firs = app(FirsApiService::class);

    $response = $firs->pullTransmittedInvoices();

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to pull transmitted invoices ', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Transmitted Invoices pulled successfully.";
    return response()->json($response);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/invoices/{irn}/download",
   *     summary="Download an invoice using IRN on FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="irn",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice confirmed successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "iv_Hex": "6c56347537795245***************",
   *                     "pub": "4hkj93SAoO6aj**********",
   *                     "data": "sLhk-38XbtyBicR9u04GXqWwOkpFHHZn5BR4sGLEc0niGZ3CT5jHgeQ7KA2wwlA8FIoARdyXp9NU7697CPZAvNqaJJopDL9glX4xHd1eLKtzTgxQVq_a782P_lK5B1iBrSW7FfxEWxTbcpi-9GY38dq1AGahbpzo0rOAabJi0y-LK2c71oZJcW4eGmo0AG2X9WIwBz_RcYMcfgUy3wpuAgIiVnBWnOlXbFocrcRKUk6ujOWIUE74wTmrdfPAVOpqyLeSdc2IMje1jPvd1SirW5WN-L4mhiU2Cj90DHLllIGhSMreoLzYdfTyaMvGlCwHstM0rJZ8S6iwfYW9IzjaOvoyZhq7FFwUaWcvihbkeOQp-Y-8oTPrXBYNMaL8rm7RnlQanHvW8AAuYjXkaet2uFOe4pEuQKI8gb6ThtNCGQU8fn1y0uomgjvjbPjtvxL72BkI01IjJMPu5uVi2bHa-ndqq2KD5tjro-gQTJ2zktXXaH10-LrQJ6***************************************************************************************"
   *                 },
   *                 "message": "Invoice download from FIRS is successful"
   *             }
   *         )
   *     )
   * )
   */
  public function download(string $irn): JsonResponse
  {
    $firs = app(FirsApiService::class);

    $response = $firs->download($irn);

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to download invoice from FIRS', $response, $response['code'] ?? 422);
    }

    $response["message"] = "Invoice download from FIRS is successful";
    return response()->json($response);
  }

  /**
   * @OA\Patch(
   *     path="/api/v1/invoices/transmit/{irn}/confirmation",
   *     summary="Acknowledge an invoice transmission with FIRS",
   *     security={{"sanctum":{}}},
   *     tags={"Invoices"},
   *     @OA\Parameter(
   *         name="irn",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={
   *                 "agent_id",
   *                 "base_amount",
   *                 "beneficiary_tin",
   *                 "currency",
   *                 "item_description",
   *                 "irn",
   *                 "total_amount",
   *                 "transaction_date",
   *                 "integrator_service_id",
   *                 "vat_calculated",
   *                 "vat_rate",
   *                 "vat_status"
   *             },
   *             @OA\Property(property="agent_id", type="string", example="01060087-0001"),
   *             @OA\Property(property="base_amount", type="string", example="100000.00"),
   *             @OA\Property(property="beneficiary_tin", type="string", example="01060087-0001"),
   *             @OA\Property(property="currency", type="string", example="NGN"),
   *             @OA\Property(property="item_description", type="string", example="Items"),
   *             @OA\Property(property="irn", type="string", example="1234567890"),
   *             @OA\Property(property="other_taxes", type="string", example="5000.00"),
   *             @OA\Property(property="total_amount", type="string", example="112500.00"),
   *             @OA\Property(property="transaction_date", type="string", format="date", example="2024-11-18"),
   *             @OA\Property(property="integrator_service_id", type="string", example="772392"),
   *             @OA\Property(property="vat_calculated", type="string", example="7500.00"),
   *             @OA\Property(property="vat_rate", type="string", example="7.5"),
   *             @OA\Property(property="vat_status", type="string", example="STANDARD_VAT")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Invoice acknowledgment submitted successfully",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 200,
   *                 "data": {
   *                     "status": "acknowledged",
   *                     "irn": "1234567890"
   *                 },
   *                 "message": "Invoice acknowledgment sent to FIRS successfully."
   *             }
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error or FIRS acknowledgment failed",
   *         @OA\JsonContent(
   *             example={
   *                 "code": 422,
   *                 "message": "Failed to acknowledge invoice with FIRS",
   *                 "errors": {}
   *             }
   *         )
   *     )
   * )
   */
  public function acknowledge(string $irn): JsonResponse
  {
    $validated = request()->validate([
      'agent_id' => 'required|string',
      'base_amount' => 'required|numeric',
      'beneficiary_tin' => 'required|string',
      'currency' => 'required|string|size:3',
      'item_description' => 'required|string',
      'irn' => 'required|string',
      'other_taxes' => 'nullable|numeric',
      'total_amount' => 'required|numeric',
      'transaction_date' => 'required|date',
      'integrator_service_id' => 'required|string',
      'vat_calculated' => 'required|numeric',
      'vat_rate' => 'required|numeric',
      'vat_status' => 'required|string',
    ]);

    $firs = app(FirsApiService::class);

    $response = $firs->confirmInvoiceTransmission($irn, $validated);

    if (($response['code'] ?? 500) != 200) {
      return $this->sendError('Failed to acknowledge invoice with FIRS', $response, $response['code'] ?? 422);
    }

    $response['message'] = 'Invoice acknowledgment sent to FIRS successfully.';
    $response['data'] = [
      'status' => 'acknowledged',
      'irn' => $irn,
    ];

    return response()->json($response);
  }
}
