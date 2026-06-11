<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BlacklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target' => [
                'required',
                'string',
                'max:253',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $v = trim((string) $value);

                    // Allow email (will be extracted)
                    if (str_contains($v, '@')) {
                        $v = substr($v, strpos($v, '@') + 1);
                    }
                    $v = preg_replace('~^https?://~i', '', $v) ?? $v;
                    $v = explode('/', $v)[0];

                    if (
                        ! filter_var($v, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                        && ! preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $v)
                    ) {
                        $fail(__('tools.blacklist_checker.error_target_invalid'));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'target.required' => __('tools.blacklist_checker.error_target_required'),
            'target.max'      => __('tools.blacklist_checker.error_target_too_long'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view('tools.partials.blacklist-checker-result', [
                'result'           => null,
                'validationErrors' => $validator->errors()->all(),
            ])
        );
    }
}
