<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmailDeliverabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain' => [
                'required',
                'string',
                'max:253',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $v = trim($value);
                    // Accept email address (extract domain part)
                    if (str_contains($v, '@')) {
                        $v = substr($v, strpos($v, '@') + 1);
                    }
                    $v = preg_replace('~^https?://~i', '', $v) ?? $v;
                    $v = explode('/', $v)[0];
                    // RFC hostname pattern
                    if (! preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $v)) {
                        $fail(__('tools.email_deliverability.error_domain_invalid'));
                    }
                },
            ],
            'dkim_selector' => ['nullable', 'string', 'max:63'],
        ];
    }

    public function messages(): array
    {
        return [
            'domain.required' => __('tools.email_deliverability.error_domain_required'),
            'domain.max'      => __('tools.email_deliverability.error_domain_too_long'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view('tools.partials.email-deliverability-result', [
                'result'           => null,
                'validationErrors' => $validator->errors()->all(),
            ])
        );
    }
}
