<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'customer_id',
        'business_id',
        'irn',
        'issue_date',
        'due_date',
        'issue_time',
        'invoice_type_code',
        'payment_status',
        'note',
        'tax_point_date',
        'document_currency_code',
        'tax_currency_code',
        'accounting_cost',
        'buyer_reference',
        'invoice_delivery_period',
        'billing_reference',
        'dispatch_document_reference',
        'receipt_document_reference',
        'originator_document_reference',
        'contract_document_reference',
        'additional_document_reference',
        'actual_delivery_date',
        'payment_means',
        'payment_terms_note',
        'allowance_charge',
        'tax_total',
        'legal_monetary_total',
        'buyer_organization_ref',
        'total_amount',
        'tax_breakdown',
        'vat_treatment',
        'wht_amount',
        'status',
    ];

    protected $casts = [
        'tax_breakdown' => 'array',
        'invoice_delivery_period' => 'array',
        'billing_reference' => 'array',
        'dispatch_document_reference' => 'array',
        'receipt_document_reference' => 'array',
        'originator_document_reference' => 'array',
        'contract_document_reference' => 'array',
        'additional_document_reference' => 'array',
        'payment_means' => 'array',
        'allowance_charge' => 'array',
        'tax_total' => 'array',
        'legal_monetary_total' => 'array',
        'issue_date' => 'date',
        'due_date' => 'date',
        'tax_point_date' => 'date',
        'actual_delivery_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                DB::transaction(function () use ($invoice) {
                    // Lock the invoices table to prevent race conditions
                    $latestInvoice = DB::table('invoices')
                        ->select('invoice_number')
                        ->whereNotNull('invoice_number')
                        ->orderByDesc('id')
                        ->lockForUpdate()
                        ->first();

                    if ($latestInvoice && preg_match('/INV(\d+)/', $latestInvoice->invoice_number, $matches)) {
                        $next = intval($matches[1]) + 1;
                    } else {
                        $next = 1;
                    }

                    $invoice->invoice_number = 'INV' . str_pad($next, 6, '0', STR_PAD_LEFT);
                });
            }
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function irn()
    {
        return $this->hasOne(Irns::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function acceptances()
    {
        return $this->hasMany(Acceptance::class);
    }

    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'entity_ref');
    }

    // Business Logic Helpers
    public function markAsValidated(): void
    {
        $this->status = 'validated';
        $this->save();
    }

    public function markAsSubmitted(): void
    {
        $this->status = 'submitted';
        $this->save();
    }

    public function markAsReported(): void
    {
        $this->status = 'reported';
        $this->save();
    }

    public function markAsClosed(): void
    {
        $this->status = 'closed';
        $this->save();
    }

    // Build FIRS payload
    public function toFirsPayload(): array
    {
        return [
            "business_id" => $this->business_id,
            "irn" => $this->irn,
            "issue_date" => optional($this->issue_date)->format('Y-m-d'),
            "due_date" => optional($this->due_date)->format('Y-m-d'),
            "issue_time" => $this->issue_time,
            "invoice_type_code" => $this->invoice_type_code,
            "payment_status" => $this->payment_status,
            "note" => $this->note,
            "tax_point_date" => optional($this->tax_point_date)->format('Y-m-d'),
            "document_currency_code" => $this->document_currency_code,
            "tax_currency_code" => $this->tax_currency_code,
            "accounting_cost" => $this->accounting_cost,
            "buyer_reference" => $this->buyer_reference,
            "invoice_delivery_period" => $this->invoice_delivery_period,
            "billing_reference" => $this->billing_reference,
            "dispatch_document_reference" => $this->dispatch_document_reference,
            "receipt_document_reference" => $this->receipt_document_reference,
            "originator_document_reference" => $this->originator_document_reference,
            "contract_document_reference" => $this->contract_document_reference,
            "additional_document_reference" => $this->additional_document_reference,
            "actual_delivery_date" => optional($this->actual_delivery_date)->format('Y-m-d'),
            "payment_means" => $this->payment_means,
            "payment_terms_note" => $this->payment_terms_note,
            "allowance_charge" => $this->allowance_charge,
            "tax_total" => $this->tax_total,
            "legal_monetary_total" => $this->legal_monetary_total,

            // Parties (for now assume you map from Organization & Buyer)
            "accounting_supplier_party" => $this->organization?->toPartyObject(),
            "accounting_customer_party" => $this->customer?->toPartyObject(),

            // Lines
            "invoice_line" => $this->items->map->toFirsPayload()->toArray(),
        ];
    }

    // Example buyer mapping (adjust to your model)
    public function buyerParty(): array
    {
        return [
            "party_name" => $this->buyer_organization_ref,
            "tin" => $this->organization?->tin,
            "email" => $this->customer?->name,
            "telephone" => $this->customer?->phone,
            "business_description" => $this->customer?->business_description ?? null,
            "postal_address" => [
                "street_name" => $this->customer?->street_name ?? null,
                "city_name" => $this->customer?->city_name ?? null,
                "postal_zone" => $this->customer?->postal_zone ?? null,
                "country" => $this->customer?->country ?? "NG",
            ]
        ];
    }
}
