<?php

namespace App\Http\Requests\Tools;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PortReferenceRequest extends FormRequest
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
            'q'        => ['nullable', 'string', 'max:50'],
            'protocol' => ['nullable', Rule::in(['all', 'tcp', 'udp'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Default protocollo se assente o vuoto.
        if (! $this->filled('protocol')) {
            $this->merge(['protocol' => 'all']);
        }
    }
}
