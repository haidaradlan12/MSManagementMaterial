<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
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
            'material_code' => 'required|string|max:255|unique:materials,material_code,' . $this->material->id,
            'material_name' => 'required|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'remarks' => 'nullable|string',
        ];
    }
}
