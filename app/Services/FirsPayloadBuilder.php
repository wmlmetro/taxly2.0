<?php

namespace App\Services;

use App\Models\Invoice;

class FirsPayloadBuilder
{
  public static function fromInvoice(Invoice $invoice): array
  {
    return [
      "business_id" => $invoice->organization?->business_id ?? "TEST_BUSINESS_ID",
      "irn" => $invoice->irn?->irn_hash ?? null,
      "issue_date" => $invoice->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
      "due_date" => $invoice->due_date?->format('Y-m-d'),
      "issue_time" => $invoice->issue_time ?? now()->format('H:i:s'),
      "invoice_type_code" => $invoice->invoice_type_code ?? "396",
      "payment_status" => $invoice->payment_status ?? "PENDING",
      "note" => $invoice->note,
      "tax_point_date" => $invoice->tax_point_date?->format('Y-m-d'),
      "document_currency_code" => $invoice->document_currency_code ?? "NGN",
      "tax_currency_code" => $invoice->tax_currency_code ?? "NGN",
      "accounting_cost" => $invoice->accounting_cost,

      "buyer_reference" => $invoice->buyer_organization_ref,

      // Supplier
      "accounting_supplier_party" => [
        "party_name" => $invoice->organization->name,
        "tin" => $invoice->organization->tin,
        "email" => $invoice->organization->email,
        "telephone" => $invoice->organization->phone,
        "business_description" => $invoice->organization->description,
        "postal_address" => [
          "street_name" => $invoice->organization->street_name,
          "city_name" => $invoice->organization->city_name,
          "postal_zone" => $invoice->organization->postal_code,
          "country" => $invoice->organization->country ?? "NG",
        ],
      ],

      // Customer
      "accounting_customer_party" => [
        "party_name" => $invoice->customer_name,
        "tin" => $invoice->buyer_organization_ref,
        "email" => $invoice->customer_email,
        "telephone" => $invoice->customer_phone,
        "business_description" => $invoice->customer_description,
        "postal_address" => [
          "street_name" => $invoice->customer_street_name,
          "city_name" => $invoice->customer_city_name,
          "postal_zone" => $invoice->customer_postal_code,
          "country" => $invoice->customer_country ?? "NG",
        ],
      ],

      // Payment Means
      "payment_means" => $invoice->payment_means ?? [
        [
          "payment_means_code" => "10",
          "payment_due_date" => $invoice->due_date?->format('Y-m-d'),
        ],
      ],

      // Allowance / Charges
      "allowance_charge" => $invoice->allowance_charge ?? [
        [
          "charge_indicator" => true,
          "amount" => 0,
        ],
      ],

      // Tax total
      "tax_total" => [
        [
          "tax_amount" => array_sum($invoice->tax_breakdown ?? []),
          "tax_subtotal" => collect($invoice->tax_breakdown ?? [])->map(fn($amount, $key) => [
            "taxable_amount" => $invoice->total_amount - $amount,
            "tax_amount" => $amount,
            "tax_category" => [
              "id" => $key,
              "percent" => null,
            ]
          ])->values()->toArray(),
        ]
      ],

      // Monetary totals
      "legal_monetary_total" => [
        "line_extension_amount" => $invoice->items->sum('line_total'),
        "tax_exclusive_amount" => $invoice->total_amount,
        "tax_inclusive_amount" => $invoice->total_amount + array_sum($invoice->tax_breakdown ?? []),
        "payable_amount" => $invoice->total_amount,
      ],

      // Invoice lines
      "invoice_line" => $invoice->items->map(fn($item) => [
        "hsn_code" => $item->hsn_code ?? "GEN-001",
        "product_category" => $item->product_category ?? "General",
        "discount_rate" => $item->discount_rate ?? 0,
        "discount_amount" => $item->discount_amount ?? 0,
        "fee_rate" => $item->fee_rate ?? 0,
        "fee_amount" => $item->fee_amount ?? 0,
        "invoiced_quantity" => $item->quantity,
        "line_extension_amount" => $item->line_total,
        "item" => [
          "name" => $item->item['name'] ?? $item->description,
          "description" => $item->item['description'] ?? $item->description,
          "sellers_item_identification" => $item->item['sellers_item_identification'] ?? $item->id,
        ],
        "price" => [
          "price_amount" => $item->price,
          "base_quantity" => $item->base_quantity ?? 1,
          "price_unit" => $item->price_unit ?? "NGN per 1",
        ]
      ])->toArray(),
    ];
  }
}
