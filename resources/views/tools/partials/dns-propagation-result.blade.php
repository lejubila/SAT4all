@php
    $consistent  = $result['consistent'];
    $agreeing    = $result['agreeing'];
    $responding  = $result['responding'];
    $total       = $result['total'];
    $majority    = $result['majority'];
@endphp

<div class="space-y-3">

    {{-- Summary banner --}}
    <div class="flex flex-wrap items-center gap-3 rounded-lg border
                {{ $consistent ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }}
                px-4 py-3 shadow-sm">
        <span class="text-xl">{{ $consistent ? '✔' : '⚠' }}</span>
        <div class="flex-1">
            <p class="text-sm font-semibold {{ $consistent ? 'text-emerald-800' : 'text-amber-800' }}">
                {{ $consistent
                    ? __('tools.dns_lookup.prop_status_propagated')
                    : __('tools.dns_lookup.prop_status_propagating') }}
            </p>
            <p class="text-xs {{ $consistent ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ __('tools.dns_lookup.prop_agreement', [
                    'agree' => $agreeing,
                    'total' => $responding,
                ]) }}
                @if ($responding < $total)
                    &nbsp;·&nbsp;{{ __('tools.dns_lookup.prop_timeout', ['n' => $total - $responding]) }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-1">
            <span class="rounded bg-slate-100 px-2 py-0.5 font-mono text-xs font-semibold text-slate-700">
                {{ $result['host'] }}
            </span>
            <span class="rounded bg-sky-100 px-2 py-0.5 font-mono text-xs font-semibold text-sky-700">
                {{ $result['type'] }}
            </span>
        </div>
    </div>

    {{-- Results table --}}
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-left font-medium w-8"></th>
                    <th class="px-3 py-2 text-left font-medium">{{ __('tools.dns_lookup.prop_col_server') }}</th>
                    <th class="px-3 py-2 text-left font-medium">{{ __('tools.dns_lookup.prop_col_result') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($result['results'] as $row)
                    @php
                        $status = $row['status'];
                        $rowBg  = match ($status) {
                            'mismatch' => 'bg-amber-50',
                            'nxdomain' => 'bg-slate-50',
                            default    => '',
                        };
                    @endphp
                    <tr class="{{ $rowBg }} hover:bg-slate-50/80">

                        {{-- Flag --}}
                        <td class="px-3 py-2.5 text-center">
                            <span class="text-2xl leading-none" title="{{ $row['location'] }}">{{ $row['flag'] }}</span>
                        </td>

                        {{-- Server info --}}
                        <td class="px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                {{-- Status dot --}}
                                <span class="shrink-0 text-xs leading-none">
                                    @if ($status === 'match')        <span class="text-emerald-500">●</span>
                                    @elseif ($status === 'mismatch') <span class="text-amber-500">●</span>
                                    @elseif ($status === 'nxdomain') <span class="text-slate-400">●</span>
                                    @else                            <span class="text-slate-300">●</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-medium text-slate-800 leading-tight">{{ $row['name'] }}</p>
                                    <p class="text-xs text-slate-400 leading-tight">
                                        {{ $row['location'] }}&nbsp;·&nbsp;<span class="font-mono">{{ $row['ip'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Answer --}}
                        <td class="px-3 py-2.5">
                            @if ($status === 'error')
                                <span class="text-xs italic text-slate-400">
                                    {{ $row['error'] === 'timeout'
                                        ? __('tools.dns_lookup.prop_timeout_label')
                                        : __('tools.dns_lookup.prop_error_label') }}
                                </span>
                            @elseif ($status === 'nxdomain')
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">
                                    NXDOMAIN
                                </span>
                            @elseif (empty($row['answers']))
                                <span class="text-xs italic text-slate-400">—</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($row['answers'] as $ans)
                                        <span class="rounded px-2 py-0.5 font-mono text-xs font-semibold
                                                      {{ $status === 'match'
                                                            ? 'bg-emerald-100 text-emerald-800'
                                                            : 'bg-amber-100 text-amber-800' }}">
                                            {{ $ans }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
