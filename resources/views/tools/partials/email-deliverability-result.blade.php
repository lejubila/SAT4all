@php
    $badge = function (bool $found): string {
        return $found
            ? 'bg-emerald-100 text-emerald-800 border border-emerald-300'
            : 'bg-red-100 text-red-800 border border-red-300';
    };

    $allColor = function (?string $all): string {
        return match (true) {
            $all === '-all'                => 'bg-emerald-100 text-emerald-800',
            $all === '~all'               => 'bg-amber-100 text-amber-800',
            in_array($all, ['+all', '?all'], true) => 'bg-red-100 text-red-800',
            default                        => 'bg-slate-100 text-slate-500',
        };
    };

    $policyColor = function (?string $p): string {
        return match ($p) {
            'reject'     => 'bg-emerald-100 text-emerald-800',
            'quarantine' => 'bg-amber-100 text-amber-800',
            'none'       => 'bg-red-100 text-red-800',
            default      => 'bg-slate-100 text-slate-500',
        };
    };
@endphp

{{-- Validation errors --}}
@if (! empty($validationErrors ?? null))
    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
        @foreach ($validationErrors as $msg)
            <p class="text-sm font-medium text-red-700">{{ $msg }}</p>
        @endforeach
    </div>

{{-- Results --}}
@elseif ($result !== null)

    {{-- Domain heading --}}
    <p class="mb-4 text-sm text-slate-500">
        {{ __('tools.email_deliverability.checking') }}
        <span class="font-mono font-semibold text-slate-800">{{ $result['domain'] }}</span>
    </p>

    <div class="grid gap-4 sm:grid-cols-2">

        {{-- ── MX ──────────────────────────────────────────────────────────── --}}
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">MX</h2>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge(! empty($result['mx'])) }}">
                    {{ ! empty($result['mx'])
                        ? __('tools.email_deliverability.found')
                        : __('tools.email_deliverability.not_found') }}
                </span>
            </div>
            @if (empty($result['mx']))
                <p class="text-xs text-slate-400">{{ __('tools.email_deliverability.mx_none') }}</p>
            @else
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-1 pr-3 font-semibold">{{ __('tools.email_deliverability.col_priority') }}</th>
                            <th class="pb-1 font-semibold">{{ __('tools.email_deliverability.col_host') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result['mx'] as $i => $mx)
                            <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }}">
                                <td class="py-1 pr-3 font-mono text-slate-600">{{ $mx['priority'] }}</td>
                                <td class="py-1 font-mono text-slate-800 break-all">{{ $mx['host'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ── SPF ─────────────────────────────────────────────────────────── --}}
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">SPF</h2>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge($result['spf']['found']) }}">
                    {{ $result['spf']['found']
                        ? __('tools.email_deliverability.found')
                        : __('tools.email_deliverability.not_found') }}
                </span>
            </div>
            @if ($result['spf']['found'])
                <code class="block break-all rounded bg-slate-50 p-2 text-xs text-slate-700">
                    {{ $result['spf']['record'] }}
                </code>
                @if ($result['spf']['all'] !== null)
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-slate-500">{{ __('tools.email_deliverability.spf_mechanism') }}</span>
                        <span class="rounded px-2 py-0.5 text-xs font-mono font-semibold {{ $allColor($result['spf']['all']) }}">
                            {{ $result['spf']['all'] }}
                        </span>
                    </div>
                @endif
            @else
                <p class="text-xs text-slate-400">{{ __('tools.email_deliverability.spf_none') }}</p>
            @endif
        </div>

        {{-- ── DMARC ───────────────────────────────────────────────────────── --}}
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">DMARC</h2>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge($result['dmarc']['found']) }}">
                    {{ $result['dmarc']['found']
                        ? __('tools.email_deliverability.found')
                        : __('tools.email_deliverability.not_found') }}
                </span>
            </div>
            @if ($result['dmarc']['found'])
                <code class="mb-3 block break-all rounded bg-slate-50 p-2 text-xs text-slate-700">
                    {{ $result['dmarc']['record'] }}
                </code>
                <dl class="grid grid-cols-2 gap-y-1 text-xs">
                    <dt class="text-slate-500">{{ __('tools.email_deliverability.dmarc_policy') }}</dt>
                    <dd>
                        @if ($result['dmarc']['policy'])
                            <span class="rounded px-1.5 py-0.5 font-semibold {{ $policyColor($result['dmarc']['policy']) }}">
                                {{ $result['dmarc']['policy'] }}
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </dd>
                    <dt class="text-slate-500">{{ __('tools.email_deliverability.dmarc_sp') }}</dt>
                    <dd>
                        @if ($result['dmarc']['sp'])
                            <span class="rounded px-1.5 py-0.5 font-semibold {{ $policyColor($result['dmarc']['sp']) }}">
                                {{ $result['dmarc']['sp'] }}
                            </span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </dd>
                    <dt class="text-slate-500">{{ __('tools.email_deliverability.dmarc_pct') }}</dt>
                    <dd class="text-slate-700">{{ $result['dmarc']['pct'] !== null ? $result['dmarc']['pct'] . '%' : '100%' }}</dd>
                    @if ($result['dmarc']['rua'])
                        <dt class="text-slate-500">{{ __('tools.email_deliverability.dmarc_rua') }}</dt>
                        <dd class="break-all font-mono text-slate-700">{{ $result['dmarc']['rua'] }}</dd>
                    @endif
                </dl>
            @else
                <p class="text-xs text-slate-400">{{ __('tools.email_deliverability.dmarc_none') }}</p>
            @endif
        </div>

        {{-- ── DKIM ────────────────────────────────────────────────────────── --}}
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">DKIM</h2>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge($result['dkim']['found']) }}">
                    {{ $result['dkim']['found']
                        ? __('tools.email_deliverability.found')
                        : __('tools.email_deliverability.not_found') }}
                </span>
            </div>
            <div class="mb-2 flex items-center gap-2 text-xs text-slate-500">
                <span>{{ __('tools.email_deliverability.dkim_selector') }}</span>
                @if ($result['dkim']['selector'])
                    <span class="font-mono font-semibold text-slate-700">{{ $result['dkim']['selector'] }}</span>
                    @if ($result['dkim']['auto'] ?? false)
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-500">auto</span>
                    @endif
                @else
                    <span class="text-slate-400">{{ __('tools.email_deliverability.dkim_not_found_selector') }}</span>
                @endif
            </div>
            @if ($result['dkim']['found'])
                <code class="block break-all rounded bg-slate-50 p-2 text-xs text-slate-700">
                    {{ $result['dkim']['record'] }}
                </code>
            @else
                <p class="text-xs text-slate-400">{{ __('tools.email_deliverability.dkim_none') }}</p>
            @endif
        </div>

    </div>

@endif
