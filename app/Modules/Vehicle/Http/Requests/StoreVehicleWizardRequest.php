<?php

namespace App\Modules\Vehicle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleWizardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:80',
            'year' => 'required|integer|min:1900|max:'.now()->year + 1,
            'plate' => 'required|string|max:10|unique:vehicles,plate',
            'vin' => 'nullable|string|max:17|unique:vehicles,vin',
            'registration_date' => 'nullable|date|before:today',
            'fuel_type' => 'required|in:gasolina,diesel,hibrido,electrico,glp',
            'eco_label' => 'nullable|in:ECO,C,B,Zero',
            'emissions_co2' => 'nullable|integer|min:0|max:500',
            'official_consumption' => 'nullable|numeric|min:0|max:20',
            'color' => 'nullable|string|max:40',
            'engine_cc' => 'nullable|integer|min:500|max:8000',
            'power_hp' => 'nullable|integer|min:30|max:1000',
            'torque_nm' => 'nullable|integer|min:50|max:1500',
            'transmission' => 'nullable|in:manual,automatico,cvt',
            'drive' => 'nullable|in:fwd,rwd,4wd,awd',
            'doors' => 'nullable|integer|min:2|max:5',
            'seats' => 'nullable|integer|min:2|max:9',
            'current_km' => 'required|integer|min:0',
            'purchase_date' => 'nullable|date|before:today',
            'purchase_price' => 'nullable|decimal:0,2|min:0',
            'photo' => 'nullable|image|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'plate.unique' => 'Esta matrícula ya está registrada.',
            'vin.unique' => 'Este número de bastidor ya está registrado.',
            'registration_date.before' => 'La fecha de matriculación debe ser anterior a hoy.',
            'purchase_date.before' => 'La fecha de compra debe ser anterior a hoy.',
            'eco_label.in' => 'La etiqueta medioambiental no es válida.',
            'emissions_co2.max' => 'Las emisiones de CO2 no pueden superar 500 g/km.',
            'official_consumption.max' => 'El consumo oficial no puede superar 20 l/100km.',
        ];
    }
}
