<?php

namespace App\Http\Requests\Tools;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VlanRequest extends FormRequest
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
            'network'     => ['required', 'ipv4'],
            'base_cidr'   => ['required', 'integer', 'between:0,30'],
            'subnet_cidr' => [
                'required',
                'integer',
                'between:1,32',
                // Il prefisso subnet deve essere >= prefisso base.
                function (string $attr, mixed $value, \Closure $fail): void {
                    if ((int) $value <= (int) $this->input('base_cidr')) {
                        $fail(__('tools.vlan_calculator.error_subnet_too_small'));
                    }
                },
            ],
            'start_vlan'  => ['required', 'integer', 'between:1,4094'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'network.required'     => __('tools.vlan_calculator.error_network_required'),
            'network.ipv4'         => __('tools.vlan_calculator.error_network_invalid'),
            'base_cidr.required'   => __('tools.vlan_calculator.error_base_cidr_required'),
            'base_cidr.between'    => __('tools.vlan_calculator.error_base_cidr_range'),
            'subnet_cidr.required' => __('tools.vlan_calculator.error_subnet_cidr_required'),
            'subnet_cidr.between'  => __('tools.vlan_calculator.error_subnet_cidr_range'),
            'start_vlan.required'  => __('tools.vlan_calculator.error_start_vlan_required'),
            'start_vlan.between'   => __('tools.vlan_calculator.error_start_vlan_range'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->view('tools.partials.vlan-result', [
                'result'           => null,
                'validationErrors' => $validator->errors(),
            ])
        );
    }
}
