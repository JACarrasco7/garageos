<?php

namespace App\Modules\Documents\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:factura,itv,seguro,impuesto,otro'],
            'title' => ['nullable', 'string', 'max:150'],
            'file' => ['required', 'file', 'max:5120'], // 5MB
            'km_at_time' => ['nullable', 'integer', 'min:0'],
            'document_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:document_date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'parsed_data' => ['nullable', 'array'],
        ];
    }
}
