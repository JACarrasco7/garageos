<?php

namespace App\Modules\VehicleImport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plate_original' => ['nullable', 'string', 'max:20'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'engine_cc' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'power_kw' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'co2_emissions' => ['nullable', 'integer', 'min:0', 'max:500'],
            'origin_country' => ['nullable', 'string', 'size:2'],
        ];
    }
}
