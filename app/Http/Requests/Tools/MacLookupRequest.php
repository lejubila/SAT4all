<?php

namespace App\Http\Requests\Tools;

use App\Tools\MacLookup\MacLookup;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MacLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'mac.required' => __('tools.mac_lookup.error_required'),
            'mac.max'      => __('tools.mac_lookup.error_invalid'),
        ];
    }

    public function rules(): array
    {
        return [
            'mac' => [
                'required',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! MacLookup::validate($value)) {
                        $fail(__('tools.mac_lookup.error_invalid'));
                    }
                },
            ],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        $response = response()->view(
            'tools.partials.mac-lookup-result',
            ['errors' => $validator->errors(), 'result' => null],
            200
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
