<?php

namespace App\Http\Requests\Tools;

use App\Tools\Whois\Whois;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WhoisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'target.required' => __('tools.whois.error_target_required'),
            'target.max'      => __('tools.whois.error_target_invalid'),
        ];
    }

    public function rules(): array
    {
        return [
            'target' => [
                'required',
                'string',
                'max:253',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! Whois::validateTarget($value)) {
                        $fail(__('tools.whois.error_target_invalid'));
                    }
                },
            ],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $response = response()->view(
            'tools.partials.whois-result',
            ['errors' => $validator->errors(), 'result' => null],
            200
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
