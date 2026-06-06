<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegexTesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pattern'     => ['nullable', 'string', 'max:2000'],
            'flags'       => ['nullable', 'string', 'max:10', 'regex:/^[imsuxX]*$/'],
            'subject'     => ['nullable', 'string', 'max:10000'],
            'replacement' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view(
                'tools.partials.regex-tester-result',
                ['result' => ['idle' => false, 'valid' => false, 'error' => $validator->errors()->first()]],
                200
            )
        );
    }
}
