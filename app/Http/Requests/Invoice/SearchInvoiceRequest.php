<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class SearchInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Core FIRS fields
            'buyer_organization_ref' => ['required', 'string'],
            'total_amount'           => ['required', 'numeric', 'min:0'],
            'status'                 => ['nullable', 'string'],

            // Parties (FIRS requires supplier & customer)
            'accounting_supplier_party' => ['required', 'array'],
            'accounting_supplier_party.party_name' => ['required', 'string'],
            'accounting_supplier_party.tin'        => ['required', 'string'],
            'accounting_supplier_party.email'      => ['required', 'email'],
            'accounting_supplier_party.telephone'  => ['nullable', 'string'],
            'accounting_supplier_party.postal_address' => ['required', 'array'],

            'accounting_customer_party' => ['required', 'array'],
            'accounting_customer_party.party_name' => ['required', 'string'],
            'accounting_customer_party.tin'        => ['required', 'string'],
            'accounting_customer_party.email'      => ['required', 'email'],
            'accounting_customer_party.telephone'  => ['nullable', 'string'],
            'accounting_customer_party.postal_address' => ['required', 'array'],

            // Invoice metadata
            'irn'               => ['nullable', 'string'],
            'issue_date'        => ['required', 'date'],
            'due_date'          => ['nullable', 'date'],
            'issue_time'        => ['nullable', 'date_format:H:i:s'],
            'invoice_type_code' => ['required', 'string'],
            'payment_status'    => ['nullable', 'string', 'in:PENDING,PAID,CANCELLED'],
            'note'              => ['nullable', 'string'],
            'tax_point_date'    => ['nullable', 'date'],
            'document_currency_code' => ['required', 'string'],
            'tax_currency_code'      => ['nullable', 'string'],

            // Monetary totals
            'legal_monetary_total' => ['required', 'array'],
            'legal_monetary_total.line_extension_amount' => ['required', 'numeric'],
            'legal_monetary_total.tax_exclusive_amount'  => ['required', 'numeric'],
            'legal_monetary_total.tax_inclusive_amount'  => ['required', 'numeric'],
            'legal_monetary_total.payable_amount'        => ['required', 'numeric'],

            // Invoice lines
            'invoice_line'   => ['required', 'array', 'min:1'],
            'invoice_line.*.hsn_code'          => ['required', 'string'],
            'invoice_line.*.product_category'  => ['required', 'string'],
            'invoice_line.*.invoiced_quantity' => ['required', 'numeric', 'min:1'],
            'invoice_line.*.line_extension_amount' => ['required', 'numeric'],
            'invoice_line.*.item'              => ['required', 'array'],
            'invoice_line.*.item.name'         => ['required', 'string'],
            'invoice_line.*.item.description'  => ['required', 'string'],
            'invoice_line.*.price'             => ['required', 'array'],
            'invoice_line.*.price.price_amount' => ['required', 'numeric'],
            'invoice_line.*.price.price_unit'  => ['required', 'string'],

            // Optional: taxes, payments, allowances
            'tax_total'        => ['nullable', 'array'],
            'payment_means'    => ['nullable', 'array'],
            'allowance_charge' => ['nullable', 'array'],
        ];
    }
}
