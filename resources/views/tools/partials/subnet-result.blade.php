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
            'result_network'   => $result['network'].'/'.$result['cidr'],
            'result_broadcast' => $result['broadcast'],
            'result_netmask'   => $result['netmask'],
            'result_wildcard'  => $result['wildcard'],
            'result_host_min'  => $result['host_min'],
            'result_host_max'  => $result['host_max'],
            'result_usable'    => number_format($result['usable_hosts'], 0, '.', ' '),
            'result_total'     => number_format($result['total_hosts'], 0, '.', ' '),
            'result_class'     => $result['ip_class'],
        ];
    @endphp

    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">
                {{ __('tools.subnet_calculator.result_title') }}
            </h2>
            <span class="rounded px-2 py-0.5 text-xs font-medium
                         {{ $result['is_private']
                                ? 'bg-amber-100 text-amber-700'
                                : 'bg-sky-100 text-sky-700' }}">
                {{ $result['is_private']
                    ? __('tools.subnet_calculator.type_private')
                    : __('tools.subnet_calculator.type_public') }}
            </span>
        </div>

        <dl class="divide-y divide-slate-100">
            @foreach ($rows as $key => $value)
                <div class="flex items-center justify-between gap-4 py-2">
                    <dt class="text-sm text-slate-500">{{ __('tools.subnet_calculator.'.$key) }}</dt>
                    <dd class="font-mono text-sm font-medium text-slate-800">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
@endif
