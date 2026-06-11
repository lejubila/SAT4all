{{-- Validation errors --}}
@if (! empty($validationErrors ?? null))
    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
        @foreach ($validationErrors as $msg)
            <p class="text-sm font-medium text-red-700">{{ $msg }}</p>
        @endforeach
    </div>

{{-- Results --}}
@elseif ($result !== null)

    {{-- Summary bar --}}
    <div class="mb-5 flex flex-wrap items-center gap-4 rounded-lg border p-4
        {{ $result['listed_count'] === 0
            ? 'border-emerald-200 bg-emerald-50'
            : 'border-red-200 bg-red-50' }}">

        <span class="text-4xl font-extrabold
            {{ $result['listed_count'] === 0 ? 'text-emerald-600' : 'text-red-600' }}">
            {{ $result['listed_count'] }}
            <span class="text-xl font-medium text-slate-500">/ {{ $result['total_checked'] }}</span>
        </span>

        <div>
            <p class="font-semibold
                {{ $result['listed_count'] === 0 ? 'text-emerald-800' : 'text-red-800' }}">
                @if ($result['listed_count'] === 0)
                    {{ __('tools.blacklist_checker.summary_clean') }}
                @else
                    {{ __('tools.blacklist_checker.summary_listed') }}
                @endif
            </p>
            <p class="mt-0.5 text-xs text-slate-500">
                @if ($result['ip'] !== null)
                    {{ __('tools.blacklist_checker.checked_ip') }}
                    <span class="font-mono font-semibold text-slate-700">{{ $result['ip'] }}</span>
                    @if ($result['ip_resolved'])
                        <span class="ml-1 text-slate-400">({{ __('tools.blacklist_checker.resolved_from') }} {{ $result['domain'] }})</span>
                    @endif
                @elseif ($result['domain'] !== null)
                    {{ __('tools.blacklist_checker.checked_domain') }}
                    <span class="font-mono font-semibold text-slate-700">{{ $result['domain'] }}</span>
                    <span class="ml-1 text-amber-600">— {{ __('tools.blacklist_checker.ip_unresolved') }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Results table --}}
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">{{ __('tools.blacklist_checker.col_name') }}</th>
                    <th class="px-4 py-3">{{ __('tools.blacklist_checker.col_zone') }}</th>
                    <th class="px-4 py-3">{{ __('tools.blacklist_checker.col_type') }}</th>
                    <th class="px-4 py-3">{{ __('tools.blacklist_checker.col_status') }}</th>
                    <th class="px-4 py-3">{{ __('tools.blacklist_checker.col_detail') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($result['results'] as $i => $row)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} border-b border-slate-100">
                        <td class="px-4 py-2 font-medium text-slate-800">{{ $row['name'] }}</td>
                        <td class="px-4 py-2 font-mono text-xs text-slate-500">{{ $row['zone'] }}</td>
                        <td class="px-4 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $row['type'] === 'ip' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ strtoupper($row['type']) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            @if ($row['listed'])
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                    {{ __('tools.blacklist_checker.status_listed') }}
                                </span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                    {{ __('tools.blacklist_checker.status_clean') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-500">
                            @if ($row['result'])
                                <span class="font-mono text-red-600">{{ $row['result'] }}</span>
                            @endif
                            @if ($row['reason'])
                                <span class="ml-1">{{ Str::limit($row['reason'], 80) }}</span>
                            @endif
                            @if (! $row['result'] && ! $row['reason'])
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif
