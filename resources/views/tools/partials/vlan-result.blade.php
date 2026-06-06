@if (! empty($validationErrors) && $validationErrors->isNotEmpty())
    <div class="rounded-lg border border-red-200 bg-red-50 p-5">
        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($validationErrors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@elseif (! empty($result))
    <div class="space-y-3">
        {{-- Riepilogo --}}
        <div class="flex flex-wrap gap-3">
            <span class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                <span class="text-slate-500">{{ __('tools.vlan_calculator.result_base') }}: </span>
                <span class="font-mono font-semibold text-slate-800">{{ $result['base_network'] }}</span>
            </span>
            <span class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                <span class="text-slate-500">{{ __('tools.vlan_calculator.result_total') }}: </span>
                <span class="font-mono font-semibold text-slate-800">{{ number_format($result['total_subnets'], 0, '.', ' ') }}</span>
            </span>
            <span class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                <span class="text-slate-500">{{ __('tools.vlan_calculator.result_shown') }}: </span>
                <span class="font-mono font-semibold text-slate-800">{{ $result['shown'] }}</span>
            </span>
        </div>

        @if ($result['truncated'])
            <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2 text-sm text-amber-700">
                ℹ {{ __('tools.vlan_calculator.result_truncated', [
                    'max'   => \App\Tools\VlanCalculator\VlanCalculator::MAX_RESULTS,
                    'total' => number_format($result['total_subnets'], 0, '.', ' '),
                ]) }}
            </p>
        @endif

        {{-- Tabella VLAN --}}
        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-xs uppercase tracking-wide text-slate-100">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium">{{ __('tools.vlan_calculator.col_vlan') }}</th>
                        <th class="px-3 py-2 text-left font-medium">{{ __('tools.vlan_calculator.col_network') }}</th>
                        <th class="hidden px-3 py-2 text-left font-medium sm:table-cell">{{ __('tools.vlan_calculator.col_gateway') }}</th>
                        <th class="hidden px-3 py-2 text-left font-medium md:table-cell">{{ __('tools.vlan_calculator.col_broadcast') }}</th>
                        <th class="px-3 py-2 text-right font-medium">{{ __('tools.vlan_calculator.col_usable') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($result['vlans'] as $vlan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2">
                                <span class="rounded bg-sky-100 px-2 py-0.5 font-mono text-xs font-semibold text-sky-700">
                                    {{ $vlan['vlan_id'] }}
                                </span>
                            </td>
                            <td class="px-3 py-2 font-mono text-slate-800">{{ $vlan['network'] }}</td>
                            <td class="hidden px-3 py-2 font-mono text-slate-600 sm:table-cell">{{ $vlan['gateway'] }}</td>
                            <td class="hidden px-3 py-2 font-mono text-slate-500 md:table-cell">{{ $vlan['broadcast'] }}</td>
                            <td class="px-3 py-2 text-right font-mono text-slate-700">{{ number_format($vlan['usable'], 0, '.', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
