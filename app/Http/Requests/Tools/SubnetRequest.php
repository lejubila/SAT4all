<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubnetRequest extends FormRequest
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
            'ip'   => ['required', 'ipv4'],
            'cidr' => ['required', 'integer', 'between:0,32'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ip.required'   => __('tools.subnet_calculator.error_ip_required'),
            'ip.ipv4'       => __('tools.subnet_calculator.error_ip_invalid'),
            'cidr.required' => __('tools.subnet_calculator.error_cidr_required'),
            'cidr.integer'  => __('tools.subnet_calculator.error_cidr_invalid'),
            'cidr.between'  => __('tools.subnet_calculator.error_cidr_range'),
        ];
    }

    /**
     * In contesto htmx restituiamo una partial Blade con gli errori,
     * non un redirect ne' JSON.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->view('tools.partials.subnet-result', [
                'result'           => null,
                'validationErrors' => $validator->errors(),
            ])
        );
    }
}
