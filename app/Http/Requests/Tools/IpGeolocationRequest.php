<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IpGeolocationRequest extends FormRequest
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
            'ip' => ['required', 'ip'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ip.required' => __('tools.ip_geolocation.error_ip_required'),
            'ip.ip'       => __('tools.ip_geolocation.error_ip_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->view('tools.partials.ip-geolocation-result', [
                'result'           => null,
                'validationErrors' => $validator->errors(),
            ])
        );
    }
}
