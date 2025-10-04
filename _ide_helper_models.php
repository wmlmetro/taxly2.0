<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $buyer_response
 * @property string|null $reason_code
 * @property \Illuminate\Support\Carbon $timestamp
 * @property string|null $actor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereActor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereBuyerResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereReasonCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acceptance whereUpdatedAt($value)
 */
	class Acceptance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string|null $pdf_path
 * @property string|null $json_path
 * @property string|null $signature_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereJsonPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereSignaturePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artifact whereUpdatedAt($value)
 */
	class Artifact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $entity_ref
 * @property string $verb
 * @property int|null $actor_id
 * @property string|null $payload_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\User|null $actor
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereEntityRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent wherePayloadHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditEvent whereVerb($value)
 */
	class AuditEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $tin
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $business_description
 * @property string|null $street_name
 * @property string|null $city_name
 * @property string|null $postal_zone
 * @property string|null $state
 * @property string $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereBusinessDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePostalZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereStreetName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereTin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $irn
 * @property string $supplier_name
 * @property string $supplier_email
 * @property string $customer_name
 * @property string $customer_email
 * @property string|null $agent_tin
 * @property string|null $base_amount
 * @property string|null $beneficiary_tin
 * @property string $currency
 * @property string|null $item_description
 * @property string|null $other_taxes
 * @property string|null $total_amount
 * @property string|null $transaction_date
 * @property string|null $integrator_service_id
 * @property string|null $vat_calculated
 * @property string|null $vat_rate
 * @property string|null $vat_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $acknowledged_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereAcknowledgedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereAgentTin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereBaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereBeneficiaryTin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereIntegratorServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereIrn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereOtherTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereSupplierEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereVatCalculated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereVatRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerTransmission whereVatStatus($value)
 */
	class CustomerTransmission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $invoice_number
 * @property int $organization_id
 * @property string|null $business_id
 * @property \App\Models\Irns|null $irn
 * @property \Illuminate\Support\Carbon|null $issue_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $issue_time
 * @property int $customer_id
 * @property string|null $buyer_organization_ref
 * @property string $total_amount
 * @property array<array-key, mixed>|null $tax_breakdown
 * @property string $vat_treatment
 * @property string $invoice_type_code
 * @property string $payment_status
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $tax_point_date
 * @property string $document_currency_code
 * @property string $tax_currency_code
 * @property string|null $accounting_cost
 * @property string|null $buyer_reference
 * @property array<array-key, mixed>|null $invoice_delivery_period
 * @property array<array-key, mixed>|null $billing_reference
 * @property array<array-key, mixed>|null $dispatch_document_reference
 * @property array<array-key, mixed>|null $receipt_document_reference
 * @property array<array-key, mixed>|null $originator_document_reference
 * @property array<array-key, mixed>|null $contract_document_reference
 * @property array<array-key, mixed>|null $additional_document_reference
 * @property \Illuminate\Support\Carbon|null $actual_delivery_date
 * @property array<array-key, mixed>|null $payment_means
 * @property string|null $payment_terms_note
 * @property array<array-key, mixed>|null $allowance_charge
 * @property array<array-key, mixed>|null $tax_total
 * @property array<array-key, mixed>|null $legal_monetary_total
 * @property string $wht_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Acceptance> $acceptances
 * @property-read int|null $acceptances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artifact> $artifacts
 * @property-read int|null $artifacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditEvent> $auditEvents
 * @property-read int|null $audit_events_count
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @method static \Database\Factories\InvoiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAccountingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereActualDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAdditionalDocumentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAllowanceCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBillingReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBuyerOrganizationRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBuyerReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereContractDocumentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDispatchDocumentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDocumentCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceDeliveryPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceTypeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIrn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIssueTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereLegalMonetaryTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereOriginatorDocumentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentMeans($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentTermsNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereReceiptDocumentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxBreakdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxPointDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereVatTreatment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereWhtAmount($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property int $quantity
 * @property string $price
 * @property string|null $hsn_code
 * @property string|null $product_category
 * @property string|null $discount_rate
 * @property string|null $discount_amount
 * @property string|null $fee_rate
 * @property string|null $fee_amount
 * @property int $invoiced_quantity
 * @property string|null $line_extension_amount
 * @property string|null $item_name
 * @property string|null $item_description
 * @property string|null $sellers_item_identification
 * @property string|null $price_amount
 * @property int $base_quantity
 * @property string $price_unit
 * @property string $line_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Database\Factories\InvoiceItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereBaseQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereFeeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereHsnCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereInvoicedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereItemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereLineExtensionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereLineTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem wherePriceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem wherePriceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereProductCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereSellersItemIdentification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereUpdatedAt($value)
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $irn_hash
 * @property string $qr_text
 * @property string|null $qr_image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereIrnHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereQrImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereQrText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Irns whereUpdatedAt($value)
 */
	class Irns extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\LandlordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Landlord withoutRole($roles, $guard = null)
 */
	class Landlord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $type
 * @property string $template
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organization|null $org
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $tin
 * @property string|null $business_id
 * @property string|null $service_id
 * @property string|null $trade_name
 * @property string|null $registration_number
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $street_name
 * @property string|null $city_name
 * @property string|null $postal_zone
 * @property string $country
 * @property string|null $description
 * @property array<array-key, mixed>|null $bank_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WebhookEndpoint> $webhookEndpoints
 * @property-read int|null $webhook_endpoints_count
 * @method static \Database\Factories\OrganizationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereBankDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization wherePostalZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereRegistrationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereStreetName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTradeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $channel
 * @property string|null $atrs_txn_id
 * @property string $status
 * @property int $attempts
 * @property string|null $last_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereAtrsTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereLastError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Submission whereUpdatedAt($value)
 */
	class Submission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $landlord_id
 * @property string $name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $entity_id
 * @property string|null $brand
 * @property string|null $domain
 * @property array<array-key, mixed>|null $feature_flags
 * @property string $retention_policy
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UsageMeter> $usageMeters
 * @property-read int|null $usage_meters_count
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereFeatureFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereRetentionPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property array<array-key, mixed>|null $counters
 * @property string $billing_cycle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereCounters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageMeter whereUpdatedAt($value)
 */
	class UsageMeter extends \Eloquent {}
}

namespace App\Models{
/**
 * @property \Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @method \Laravel\Sanctum\PersonalAccessToken|null currentAccessToken()
 * @property int $id
 * @property int|null $organization_id
 * @property string|null $mfa
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditEvent> $auditEvents
 * @property-read int|null $audit_events_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Organization|null $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMfa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $url
 * @property string $irn
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organization|null $org
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereIrn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookEndpoint whereUrl($value)
 */
	class WebhookEndpoint extends \Eloquent {}
}

