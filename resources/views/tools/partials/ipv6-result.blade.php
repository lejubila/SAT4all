@if (! empty($validationErrors) && $validationErrors->isNotEmpty())
    <div class="rounded-lg border border-red-200 bg-red-50 p-5">
        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($validationErrors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@elseif (! empty($result))
    @php
        $rows = [
            'result_network'          => $result['prefix_notation'],
            'result_first'            => $result['first_address'],
            'result_last'             => $result['last_address'],
            'result_compressed'       => $result['compressed'],
            'result_expanded'         => $result['expanded'],
            'result_network_expanded' => $result['network_expanded'],
            'result_total'            => $result['total_addresses'],
        ];
    @endphp

    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">
                {{ __('tools.ipv6_calculator.result_title') }}
            </h2>
            <span class="rounded bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-700">
                {{ __('tools.ipv6_calculator.type_'.$result['type']) }}
            </span>
        </div>

        <dl class="divide-y divide-slate-100">
            @foreach ($rows as $key => $value)
                <div class="flex items-center justify-between gap-4 py-2">
                    <dt class="text-sm text-slate-500">{{ __('tools.ipv6_calculator.'.$key) }}</dt>
                    <dd class="break-all text-right font-mono text-sm font-medium text-slate-800">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
@endif
