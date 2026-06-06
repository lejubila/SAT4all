<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseConverterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number'    => ['nullable', 'string', 'max:100'],
            'from_base' => ['required', 'integer', 'min:2', 'max:36'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_base.required' => __('tools.base_converter.error_base_required'),
            'from_base.integer'  => __('tools.base_converter.error_base_invalid'),
            'from_base.min'      => __('tools.base_converter.error_base_invalid'),
            'from_base.max'      => __('tools.base_converter.error_base_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view(
                'tools.partials.base-converter-result',
                ['result' => ['idle' => false, 'valid' => false, 'error' => 'validation',
                              'error_msg' => $validator->errors()->first()]],
                200
            )
        );
    }
}
