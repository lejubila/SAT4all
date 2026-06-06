<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FormatterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'input'  => ['nullable', 'string', 'max:200000'],
            'format' => ['required', Rule::in(['auto', 'json', 'xml', 'html'])],
            'indent' => ['nullable', 'integer', Rule::in([2, 4])],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view(
                'tools.partials.formatter-result',
                ['result' => ['idle' => false, 'valid' => false, 'error' => 'validation',
                              'error_detail' => $validator->errors()->first()]],
                200
            )
        );
    }
}
