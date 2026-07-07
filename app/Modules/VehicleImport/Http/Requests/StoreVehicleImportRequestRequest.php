<?php

namespace App\Modules\VehicleImport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleImportRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'fuel_type' => ['nullable', 'string', 'max:20'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'gt:budget_min'],
            'url_link' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string'],
        ];
    }
}
