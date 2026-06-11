<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MxCheckerRequest extends FormRequest
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
                    if (str_contains($v, '@')) {
                        $v = substr($v, strpos($v, '@') + 1);
                    }
                    $v = preg_replace('~^https?://~i', '', $v) ?? $v;
                    $v = explode('/', $v)[0];
                    if (! preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $v)) {
                        $fail(__('tools.mx_checker.error_domain_invalid'));
                    }
                },
            ],
            'captcha_input' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'domain.required'        => __('tools.mx_checker.error_domain_required'),
            'domain.max'             => __('tools.mx_checker.error_domain_too_long'),
            'captcha_input.required' => __('tools.mx_checker.error_captcha_required'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $expected = session('mx_checker_captcha');
            $given    = strtoupper((string) ($this->input('captcha_input') ?? ''));

            if (! $expected || $given !== strtoupper($expected)) {
                $v->errors()->add('captcha_input', __('tools.mx_checker.error_captcha'));
            }
        });
    }

    protected function failedValidation(Validator $validator): never
    {
        $newCaptcha = $this->regenerateCaptcha();

        throw new HttpResponseException(
            response()->view('tools.partials.mx-checker-result', [
                'result'           => null,
                'validationErrors' => $validator->errors()->all(),
                'newCaptcha'       => $newCaptcha,
            ])
        );
    }

    private function regenerateCaptcha(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code     = strtoupper(substr(str_shuffle($alphabet), 0, 6));
        session(['mx_checker_captcha' => $code]);

        return $code;
    }
}
