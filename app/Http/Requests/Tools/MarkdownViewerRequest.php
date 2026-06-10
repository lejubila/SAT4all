<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MarkdownViewerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'markdown' => ['nullable', 'string', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'markdown.max' => __('tools.markdown_viewer.error_too_large'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $errors = $validator->errors();
        $response = response()->view(
            'tools.partials.markdown-viewer-preview',
            ['html' => '', 'error' => $errors->first()],
            200
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
