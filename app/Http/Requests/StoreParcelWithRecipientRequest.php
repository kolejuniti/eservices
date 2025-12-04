<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParcelWithRecipientRequest extends FormRequest
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
            'ic' => ['required', 'string', 'max:20'],
            'courier' => ['required', 'exists:couriers,id'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:parcels,serial_number'],
            'parcel_size' => ['required', 'in:Kecil,Sederhana,Besar'],
            'cod' => ['nullable', 'boolean'],
            'cod_amount' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'ic.required' => 'No. KP penerima diperlukan.',
            'courier.required' => 'Jenis kurier mesti dipilih.',
            'courier.exists' => 'Jenis kurier tidak sah.',
            'serial_number.required' => 'No. siri diperlukan.',
            'serial_number.unique' => 'No. siri telah digunakan. Sila semak semula.',
            'parcel_size.required' => 'Saiz parcel mesti dipilih.',
            'parcel_size.in' => 'Saiz parcel tidak sah.',
            'cod_amount.numeric' => 'Jumlah COD mesti dalam bentuk angka.',
            'cod_amount.min' => 'Jumlah COD tidak boleh negatif.',
        ];
    }
}

