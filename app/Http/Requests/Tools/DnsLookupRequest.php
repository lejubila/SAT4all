<?php

namespace App\Http\Requests\Tools;

use App\Tools\DnsLookup\DnsLookup;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DnsLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'host' => [
                'required',
                'string',
                'max:253',
                // Accetta hostname RFC-validi oppure indirizzi IP (per query PTR).
                function (string $attr, mixed $value, \Closure $fail): void {
                    $v = trim((string) $value);
                    if (
                        ! filter_var($v, FILTER_VALIDATE_IP)
                        && ! preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $v)
                        && ! preg_match('/^[a-zA-Z0-9\-]{1,63}$/', $v)
                    ) {
                        $fail(__('tools.dns_lookup.error_host_invalid'));
                    }
                },
            ],
            'type' => ['required', Rule::in(DnsLookup::recordTypes())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'host.required' => __('tools.dns_lookup.error_host_required'),
            'host.max'      => __('tools.dns_lookup.error_host_too_long'),
            'type.required' => __('tools.dns_lookup.error_type_required'),
            'type.in'       => __('tools.dns_lookup.error_type_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->view('tools.partials.dns-result', [
                'result'           => null,
                'validationErrors' => $validator->errors(),
            ])
        );
    }
}
