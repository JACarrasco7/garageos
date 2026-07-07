<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionCheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'plan' => ['required', 'in:free,pro,importer'],
        ];
    }

    public function messages(): array
    {
        return [
            'plan.required' => 'El plan es requerido',
            'plan.in' => 'Plan inválido',
        ];
    }
}
