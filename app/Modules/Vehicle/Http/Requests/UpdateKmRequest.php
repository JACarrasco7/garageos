<?php

namespace App\Modules\Vehicle\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKmRequest extends FormRequest
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
            'km' => ['required', 'integer', 'min:0'],
            'recorded_at' => ['required', 'date'],
            'source' => ['nullable', 'string', 'in:manual,document,obd'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
