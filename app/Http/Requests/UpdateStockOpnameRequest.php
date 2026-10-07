<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStockOpnameRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'material_id' => 'required|exists:materials,id',
            'system_quantity' => 'required|integer|min:0',
            'actual_quantity' => 'required|integer|min:0',
            'opname_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}
