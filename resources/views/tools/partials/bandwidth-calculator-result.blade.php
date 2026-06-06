@php
    $valid     = $result['valid'] ?? false;
    $error     = $result['error'] ?? null;
    $mode      = $result['mode'] ?? null;
    $res       = $result['result'] ?? [];
    $extras    = $result['extras'] ?? [];
    $sizeTable = $result['size_table'] ?? [];
    $bwTable   = $result['bw_table'] ?? [];
@endphp

@if (! $valid)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        @if ($error === 'positive_required')
            {{ __('tools.bandwidth_calculator.error_positive') }}
        @else
            {{ $result['error_msg'] ?? __('tools.bandwidth_calculator.error_validation') }}
        @endif
    </div>

@else
    {{-- Primary result --}}
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-6 py-5">
        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-emerald-600">
            @if ($mode === 'time')      {{ __('tools.bandwidth_calculator.result_time') }}
            @elseif ($mode === 'size')  {{ __('tools.bandwidth_calculator.result_size') }}
            @else                       {{ __('tools.bandwidth_calculator.result_bandwidth') }}
            @endif
        </p>
        <p class="text-3xl font-bold text-emerald-900">
            @if ($mode === 'time')      {{ $res['label'] }}
            @elseif ($mode === 'size')  {{ $res['label'] }}
            @else                       {{ $res['label'] }}
            @endif
        </p>
        @if (isset($extras['throughput_bytes']))
            <p class="mt-1 text-sm text-emerald-700">
                {{ __('tools.bandwidth_calculator.throughput') }}:
                <span class="font-medium">{{ (new \App\Tools\BandwidthCalculator\BandwidthCalculator())->fmtSize($extras['throughput_bytes']) }}/s</span>
            </p>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Time breakdown (mode=time) --}}
        @if ($mode === 'time' && ! empty($res['breakdown']))
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-2">
                    <h3 class="text-sm font-semibold text-slate-700">{{ __('tools.bandwidth_calculator.result_time') }}</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">{{ __('tools.bandwidth_calculator.col_unit') }}</th>
                            <th class="px-4 py-2 text-right">{{ __('tools.bandwidth_calculator.col_value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($res['breakdown'] as $unit => $val)
                            <tr>
                                <td class="px-4 py-2 font-mono text-slate-600">{{ $unit }}</td>
                                <td class="px-4 py-2 text-right font-mono text-slate-800">{{ $val }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Size breakdown --}}
        @if (! empty($sizeTable))
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-2">
                    <h3 class="text-sm font-semibold text-slate-700">
                        {{ $mode === 'size'
                            ? __('tools.bandwidth_calculator.result_size')
                            : __('tools.bandwidth_calculator.label_file_size') }}
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">{{ __('tools.bandwidth_calculator.col_unit') }}</th>
                            <th class="px-4 py-2 text-right">{{ __('tools.bandwidth_calculator.col_value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($sizeTable as $row)
                            <tr>
                                <td class="px-4 py-2 font-mono text-slate-600">{{ $row['unit'] }}</td>
                                <td class="px-4 py-2 text-right font-mono text-slate-800">{{ $row['value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Bandwidth breakdown --}}
        @if (! empty($bwTable))
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-2">
                    <h3 class="text-sm font-semibold text-slate-700">
                        {{ $mode === 'bandwidth'
                            ? __('tools.bandwidth_calculator.result_bandwidth')
                            : __('tools.bandwidth_calculator.label_bandwidth') }}
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">{{ __('tools.bandwidth_calculator.col_unit') }}</th>
                            <th class="px-4 py-2 text-right">{{ __('tools.bandwidth_calculator.col_value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($bwTable as $row)
                            <tr>
                                <td class="px-4 py-2 font-mono text-slate-600">{{ $row['unit'] }}</td>
                                <td class="px-4 py-2 text-right font-mono text-slate-800">{{ $row['value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
@endif
