<?php

namespace App\Http\Requests\Tools;

use App\Tools\PortChecker\PortChecker;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PortCheckerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host'          => ['required', 'string', 'max:253', function (string $attr, mixed $value, \Closure $fail): void {
                if (! PortChecker::validateHost($value)) {
                    $fail(__('tools.port_checker.error_host_invalid'));
                }
            }],
            'port'          => ['required', 'integer', 'min:1', 'max:65535'],
            'protocol'      => ['required', 'in:tcp,udp'],
            'captcha_input' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'host.required'          => __('tools.port_checker.error_host_required'),
            'host.max'               => __('tools.port_checker.error_host_invalid'),
            'port.required'          => __('tools.port_checker.error_port_required'),
            'port.integer'           => __('tools.port_checker.error_port_invalid'),
            'port.min'               => __('tools.port_checker.error_port_invalid'),
            'port.max'               => __('tools.port_checker.error_port_invalid'),
            'protocol.in'            => __('tools.port_checker.error_protocol_invalid'),
            'captcha_input.required' => __('tools.port_checker.error_captcha_required'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $expected = session('port_checker_captcha');
            $given    = strtoupper((string) ($this->input('captcha_input') ?? ''));

            if (! $expected || $given !== strtoupper($expected)) {
                $v->errors()->add('captcha_input', __('tools.port_checker.error_captcha'));
            }
        });
    }

    protected function failedValidation(Validator $validator): never
    {
        $newCaptcha = $this->regenerateCaptcha();

        throw new HttpResponseException(
            response()->view(
                'tools.partials.port-checker-result',
                [
                    'result'     => null,
                    'errors'     => $validator->errors(),
                    'newCaptcha' => $newCaptcha,
                ],
                200,
            )
        );
    }

    private function regenerateCaptcha(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code     = strtoupper(substr(str_shuffle($alphabet), 0, 6));
        session(['port_checker_captcha' => $code]);

        return $code;
    }
}
