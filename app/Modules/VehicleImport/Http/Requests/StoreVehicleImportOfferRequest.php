<?php

namespace App\Modules\VehicleImport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleImportOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_import_request_id' => ['required', 'exists:vehicle_import_requests,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'delivery_time_days' => ['nullable', 'integer', 'min:1'],
            'warranty_months' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
