<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'buyer_organization_ref' => ['nullable', 'string', 'max:255'],
            'total_amount'  => ['required', 'numeric', 'min:0'],
            'tax_breakdown' => ['nullable', 'array'],
            'vat_treatment' => ['required', 'in:standard,zero-rated,exempt'],
            'wht_amount'    => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
