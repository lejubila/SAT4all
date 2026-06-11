<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmailHeaderAnalyzerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'header' => ['nullable', 'string', 'max:50000'],
        ];
    }

    public function messages(): array
    {
        return [
            'header.max' => __('tools.email_header_analyzer.error_too_large'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(
            response()->view('tools.partials.email-header-analyzer-result', [
                'result' => ['idle' => false, 'valid' => false, 'error' => 'validation', 'messages' => $errors],
            ])
        );
    }
}
