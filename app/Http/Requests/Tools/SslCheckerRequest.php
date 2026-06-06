<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SslCheckerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host' => ['required', 'string', 'max:253'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ];
    }

    public function messages(): array
    {
        return [
            'host.required' => __('tools.ssl_checker.error_host_required'),
            'host.max'      => __('tools.ssl_checker.error_host_invalid'),
            'port.integer'  => __('tools.ssl_checker.error_port_invalid'),
            'port.min'      => __('tools.ssl_checker.error_port_invalid'),
            'port.max'      => __('tools.ssl_checker.error_port_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view(
                'tools.partials.ssl-checker-result',
                ['result' => ['connected' => false, 'error' => $validator->errors()->first()]],
                200
            )
        );
    }
}
