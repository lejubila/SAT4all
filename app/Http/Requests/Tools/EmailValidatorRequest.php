<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmailValidatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'         => ['required', 'string', 'max:254'],
            'captcha_input' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'         => __('tools.email_validator.error_email_required'),
            'email.max'              => __('tools.email_validator.error_email_too_long'),
            'captcha_input.required' => __('tools.email_validator.error_captcha_required'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $expected = session('email_validator_captcha');
            $given    = strtoupper((string) ($this->input('captcha_input') ?? ''));
            if (! $expected || $given !== strtoupper($expected)) {
                $v->errors()->add('captcha_input', __('tools.email_validator.error_captcha'));
            }
        });
    }

    protected function failedValidation(Validator $validator): never
    {
        $newCaptcha = $this->regenerateCaptcha();

        throw new HttpResponseException(
            response()->view('tools.partials.email-validator-result', [
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
        session(['email_validator_captcha' => $code]);
        return $code;
    }
}
