<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBalanceRequest extends FormRequest
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
            'deduction' => 'required|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return [
            'deduction.required' => 'Deduction amount is required.',
            'deduction.numeric' => 'Deduction must be a numeric value.',
            'deduction.min' => 'Deduction must be at least 0.',
        ];
    }
}
