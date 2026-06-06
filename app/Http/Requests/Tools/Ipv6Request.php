<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Ipv6Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'address' => ['required', 'ipv6'],
            'prefix'  => ['required', 'integer', 'between:0,128'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'address.required' => __('tools.ipv6_calculator.error_address_required'),
            'address.ipv6'     => __('tools.ipv6_calculator.error_address_invalid'),
            'prefix.required'  => __('tools.ipv6_calculator.error_prefix_required'),
            'prefix.integer'   => __('tools.ipv6_calculator.error_prefix_invalid'),
            'prefix.between'   => __('tools.ipv6_calculator.error_prefix_range'),
        ];
    }

    /**
     * In contesto htmx restituiamo una partial Blade con gli errori.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->view('tools.partials.ipv6-result', [
                'result'           => null,
                'validationErrors' => $validator->errors(),
            ])
        );
    }
}
