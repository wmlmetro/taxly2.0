<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'tenant_name'   => 'required|string|max:255',
            'brand'         => 'nullable|string|max:255',
            'domain'        => 'nullable|string|max:255',
            'tin'           => ['required', 'string', 'max:50', 'unique:organizations,tin'],
            'legal_name'    => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
        ];
    }
}
