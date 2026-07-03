<?php

namespace App\Modules\Vehicle\Http\Requests;

use App\Enums\FuelType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
            'garage_id' => ['required', 'exists:garages,id'],
            'plate' => ['required', 'string', 'max:10'],
            'vin' => ['nullable', 'string', 'max:17', 'unique:vehicles,vin'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'registration_date' => ['nullable', 'date', 'before_or_equal:today'],
            'fuel_type' => ['required', 'string', 'in:'.implode(',', array_column(FuelType::cases(), 'value'))],
            'eco_label' => ['nullable', 'string', 'in:ECO,C,B,Zero'],
            'emissions_co2' => ['nullable', 'integer', 'min:0', 'max:500'],
            'official_consumption' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'color' => ['nullable', 'string', 'max:40'],
            'current_km' => ['required', 'integer', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'], // 5MB

            // Specs
            'specs.engine_cc' => ['nullable', 'integer', 'min:0'],
            'specs.power_hp' => ['nullable', 'integer', 'min:0'],
            'specs.torque_nm' => ['nullable', 'integer', 'min:0'],
            'specs.transmission' => ['nullable', 'string', 'in:manual,automatico,cvt'],
            'specs.drive' => ['nullable', 'string', 'in:fwd,rwd,4wd,awd'],
            'specs.doors' => ['nullable', 'integer', 'min:1', 'max:5'],
            'specs.seats' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fuel_type.in' => 'El tipo de combustible no es válido.',
            'eco_label.in' => 'La etiqueta medioambiental no es válida.',
            'emissions_co2.max' => 'Las emisiones de CO2 no pueden superar 500 g/km.',
            'official_consumption.max' => 'El consumo oficial no puede superar 20 l/100km.',
            'registration_date.before_or_equal' => 'La fecha de matriculación no puede ser futura.',
            'specs.transmission.in' => 'La transmisión no es válida.',
            'specs.drive.in' => 'La tracción no es válida.',
        ];
    }
}
