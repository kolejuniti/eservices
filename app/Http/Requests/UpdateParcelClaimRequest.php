<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParcelClaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pickup_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'pickup_date.required' => 'Tarikh ambil diperlukan.',
            'pickup_date.date' => 'Tarikh ambil tidak sah.',
            'pickup_date.before_or_equal' => 'Tarikh ambil tidak boleh pada masa hadapan.',
        ];
    }
}

