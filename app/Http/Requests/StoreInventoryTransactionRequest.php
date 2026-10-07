<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public form – no auth required
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:receive,take',
            'material_id' => 'nullable|exists:materials,id',
            'material_name_manual' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'person_name' => 'required|string|max:255',
            'purpose' => 'required_if:type,take|nullable|string|max:255',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'purpose.required_if' => 'Kegunaan wajib diisi untuk pengambilan barang.',
        ];
    }
}
