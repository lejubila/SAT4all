<?php

namespace App\Http\Requests\Tools;

use App\Tools\BandwidthCalculator\BandwidthCalculator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BandwidthCalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode'          => ['required', Rule::in(['time', 'size', 'bandwidth'])],
            'size_value'    => ['nullable', 'numeric', 'min:0'],
            'size_unit'     => ['nullable', Rule::in(array_keys(BandwidthCalculator::SIZE_UNITS))],
            'bw_value'      => ['nullable', 'numeric', 'min:0'],
            'bw_unit'       => ['nullable', Rule::in(array_keys(BandwidthCalculator::BW_UNITS))],
            'time_value'    => ['nullable', 'numeric', 'min:0'],
            'time_unit'     => ['nullable', Rule::in(array_keys(BandwidthCalculator::TIME_UNITS))],
            'overhead'      => ['nullable', 'numeric', 'min:0', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'mode.in'       => __('tools.bandwidth_calculator.error_validation'),
            'mode.required' => __('tools.bandwidth_calculator.error_validation'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->view(
                'tools.partials.bandwidth-calculator-result',
                ['result' => ['valid' => false, 'error' => 'validation',
                              'error_msg' => $validator->errors()->first()]],
                200
            )
        );
    }
}
