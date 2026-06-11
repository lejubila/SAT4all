@php
    use App\Tools\EmailHeaderAnalyzer\EmailHeaderAnalyzer;

    $authColor = function (?string $v): string {
        return match ($v) {
            'pass'              => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'fail'              => 'bg-red-100 text-red-800 border-red-300',
            'softfail'          => 'bg-amber-100 text-amber-800 border-amber-300',
            'neutral', 'none'   => 'bg-slate-100 text-slate-600 border-slate-300',
            default             => 'bg-slate-100 text-slate-500 border-slate-200',
        };
    };

    $delayColor = function (?int $s): string {
        if ($s === null) return '';
        if ($s < 30)     return 'bg-emerald-100 text-emerald-800';
        if ($s < 300)    return 'bg-amber-100 text-amber-800';
        return 'bg-red-100 text-red-800';
    };
@endphp

{{-- Idle --}}
@if ($result['idle'] ?? false)
    <p class="text-sm text-slate-400">{{ __('tools.email_header_analyzer.empty') }}</p>

{{-- Validation / parse error --}}
@elseif (! ($result['valid'] ?? true))
    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
        @if (($result['error'] ?? '') === 'no_headers')
            <p class="text-sm font-medium text-red-700">{{ __('tools.email_header_analyzer.error_no_headers') }}</p>
        @elseif (($result['error'] ?? '') === 'validation')
            @foreach ($result['messages'] ?? [] as $msg)
                <p class="text-sm font-medium text-red-700">{{ $msg }}</p>
            @endforeach
        @endif
    </div>

{{-- Full result --}}
@else

    {{-- ── Summary ────────────────────────────────────────────────────────── --}}
    <section class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
            {{ __('tools.email_header_analyzer.section_summary') }}
        </h2>
        <dl class="grid gap-2 sm:grid-cols-2">
            @foreach ([
                'from'       => __('tools.email_header_analyzer.field_from'),
                'to'         => __('tools.email_header_analyzer.field_to'),
                'subject'    => __('tools.email_header_analyzer.field_subject'),
                'date'       => __('tools.email_header_analyzer.field_date'),
                'message_id' => __('tools.email_header_analyzer.field_message_id'),
                'reply_to'   => __('tools.email_header_analyzer.field_reply_to'),
                'mailer'     => __('tools.email_header_analyzer.field_mailer'),
            ] as $key => $label)
                @php $val = $result['summary'][$key] ?? null; @endphp
                @if ($val !== null)
                    <div class="flex flex-col">
                        <dt class="text-xs font-semibold text-slate-500">{{ $label }}</dt>
                        <dd class="break-all font-mono text-xs text-slate-800">{{ $val }}</dd>
                    </div>
                @endif
            @endforeach
        </dl>
    </section>

    {{-- ── Delivery trace ──────────────────────────────────────────────────── --}}
    <section class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
            {{ __('tools.email_header_analyzer.section_trace') }}
        </h2>

        @if (empty($result['hops']))
            <p class="text-sm text-slate-400">{{ __('tools.email_header_analyzer.no_hops') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3 font-semibold">{{ __('tools.email_header_analyzer.col_hop') }}</th>
                            <th class="pb-2 pr-3 font-semibold">{{ __('tools.email_header_analyzer.col_from') }}</th>
                            <th class="pb-2 pr-3 font-semibold">{{ __('tools.email_header_analyzer.col_by') }}</th>
                            <th class="pb-2 pr-3 font-semibold">{{ __('tools.email_header_analyzer.col_timestamp') }}</th>
                            <th class="pb-2 font-semibold">{{ __('tools.email_header_analyzer.col_delay') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result['hops'] as $i => $hop)
                            <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} border-b border-slate-100">
                                <td class="py-1.5 pr-3 font-semibold text-slate-500">{{ $i + 1 }}</td>
                                <td class="py-1.5 pr-3 font-mono text-slate-700 break-all">{{ $hop['from'] ?: '—' }}</td>
                                <td class="py-1.5 pr-3 font-mono text-slate-700 break-all">{{ $hop['by'] ?: '—' }}</td>
                                <td class="py-1.5 pr-3 text-slate-600 whitespace-nowrap">{{ $hop['timestamp'] ?: '—' }}</td>
                                <td class="py-1.5">
                                    @if ($hop['delay_seconds'] !== null)
                                        <span class="rounded px-1.5 py-0.5 text-xs font-medium {{ $delayColor($hop['delay_seconds']) }}">
                                            {{ EmailHeaderAnalyzer::formatDelay($hop['delay_seconds']) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if ($result['total_seconds'] !== null)
                            <tr class="border-t-2 border-slate-300 font-semibold">
                                <td colspan="4" class="py-1.5 pr-3 text-right text-slate-600">
                                    {{ __('tools.email_header_analyzer.row_total') }}
                                </td>
                                <td class="py-1.5">
                                    <span class="rounded px-1.5 py-0.5 text-xs font-medium {{ $delayColor($result['total_seconds']) }}">
                                        {{ EmailHeaderAnalyzer::formatDelay($result['total_seconds']) }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- ── Authentication ──────────────────────────────────────────────────── --}}
    <section class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
            {{ __('tools.email_header_analyzer.section_auth') }}
        </h2>

        <div class="flex flex-wrap gap-4">
            @foreach ([
                'spf'   => __('tools.email_header_analyzer.auth_spf'),
                'dkim'  => __('tools.email_header_analyzer.auth_dkim'),
                'dmarc' => __('tools.email_header_analyzer.auth_dmarc'),
            ] as $proto => $label)
                @php $val = $result['auth'][$proto] ?? null; @endphp
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs font-semibold uppercase text-slate-500">{{ $label }}</span>
                    <span class="rounded-full border px-4 py-1 text-sm font-bold {{ $authColor($val) }}">
                        {{ $val !== null ? strtoupper($val) : __('tools.email_header_analyzer.auth_none') }}
                    </span>
                </div>
            @endforeach
        </div>

        @if (! empty($result['auth']['raw']))
            <details class="mt-3">
                <summary class="cursor-pointer text-xs text-slate-400 hover:text-slate-600">
                    {{ __('tools.email_header_analyzer.auth_raw') }}
                </summary>
                <p class="mt-1 break-all font-mono text-xs text-slate-600">{{ $result['auth']['raw'] }}</p>
            </details>
        @endif
    </section>

    {{-- ── All headers ─────────────────────────────────────────────────────── --}}
    <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
             x-data="{ open: false }">
        <button type="button"
                @click="open = !open"
                class="flex w-full items-center justify-between text-sm font-semibold uppercase tracking-wide text-slate-500 hover:text-slate-700">
            <span>{{ __('tools.email_header_analyzer.section_all_headers') }} ({{ count($result['all_headers']) }})</span>
            <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
        </button>

        <div x-show="open" x-cloak class="mt-3 overflow-x-auto">
            <table class="w-full text-xs">
                <tbody>
                    @foreach ($result['all_headers'] as $i => $h)
                        <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }}">
                            <td class="w-40 py-1 pr-3 align-top font-mono font-semibold text-slate-700 break-all">
                                {{ $h['name'] }}
                            </td>
                            <td class="py-1 break-all text-slate-600">{{ $h['value'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endif
