<?php

namespace App\Modules\VehicleImport\Http\Requests;

use App\Modules\VehicleImport\Enums\ImportStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImportStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'step' => ['required', Rule::in(collect(ImportStep::cases())->pluck('value')->toArray())],
        ];
    }
}
