<?php

namespace App\Http\Requests\Tools;

use App\Tools\PingTraceroute\PingTraceroute;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PingTracerouteRequest extends FormRequest
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
            'target' => [
                'required',
                'string',
                'max:253',
                function (string $attr, mixed $value, \Closure $fail): void {
                    if (!PingTraceroute::validateTarget($value)) {
                        $fail(__('tools.ping_traceroute.error_target_invalid'));
                    }
                },
            ],
            'tool'  => ['required', 'in:ping,traceroute'],
            'count' => ['nullable', 'integer', 'in:1,2,3,4,5,10'],
            'hops'  => ['nullable', 'integer', 'in:5,10,15,20,30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'target.required' => __('tools.ping_traceroute.error_target_required'),
            'target.max'      => __('tools.ping_traceroute.error_target_invalid'),
            'tool.required'   => __('tools.ping_traceroute.error_tool_required'),
            'tool.in'         => __('tools.ping_traceroute.error_tool_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->view('tools.partials.ping-traceroute-result', [
                'result'           => null,
                'validationErrors' => $validator->errors(),
            ])
        );
    }
}
