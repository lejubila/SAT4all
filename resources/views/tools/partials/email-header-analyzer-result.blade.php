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
                                <td class="py-1.5 pr-3 break-all">
                                    @php $f = $hop['from']; @endphp
                                    @if (is_array($f))
                                        @if ($f['host']) <span class="font-mono text-slate-700">{{ $f['host'] }}</span> @endif
                                        @if ($f['rdns'] && $f['rdns'] !== $f['host']) <span class="block font-mono text-xs text-slate-500">{{ $f['rdns'] }}</span> @endif
                                        @if ($f['ip']) <span class="block font-mono text-xs text-emerald-700">[{{ $f['ip'] }}]</span> @endif
                                        @if (! $f['host'] && ! $f['rdns'] && ! $f['ip']) <span class="text-slate-400">—</span> @endif
                                    @else
                                        <span class="font-mono text-slate-700">{{ $f ?: '—' }}</span>
                                    @endif
                                </td>
                                <td class="py-1.5 pr-3 break-all">
                                    @php $b = $hop['by']; @endphp
                                    @if (is_array($b))
                                        @if ($b['host']) <span class="font-mono text-slate-700">{{ $b['host'] }}</span> @endif
                                        @if ($b['rdns'] && $b['rdns'] !== $b['host']) <span class="block font-mono text-xs text-slate-500">{{ $b['rdns'] }}</span> @endif
                                        @if ($b['ip']) <span class="block font-mono text-xs text-emerald-700">[{{ $b['ip'] }}]</span> @endif
                                        @if (! $b['host'] && ! $b['rdns'] && ! $b['ip']) <span class="text-slate-400">—</span> @endif
                                    @else
                                        <span class="font-mono text-slate-700">{{ $b ?: '—' }}</span>
                                    @endif
                                </td>
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

        <div class="grid gap-4 sm:grid-cols-3">

            {{-- SPF --}}
            @php $spf = $result['auth']['spf'] ?? []; $spfResult = $spf['result'] ?? null; @endphp
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ __('tools.email_header_analyzer.auth_spf') }}
                    </span>
                    <span class="rounded-full border px-3 py-0.5 text-xs font-bold {{ $authColor($spfResult) }}">
                        {{ $spfResult !== null ? strtoupper($spfResult) : __('tools.email_header_analyzer.auth_none') }}
                    </span>
                </div>
                @if (! empty($spf['domain']))
                    <dl class="space-y-1.5 text-xs">
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_domain') }}</dt>
                            <dd class="font-mono text-slate-700">{{ $spf['domain'] }}</dd>
                        </div>
                        @if (! empty($spf['dns_queried']))
                            <div>
                                <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_dns_name') }}</dt>
                                <dd class="break-all font-mono text-slate-700">{{ $spf['dns_queried'] }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_dns_record') }}</dt>
                                @if (! empty($spf['dns_record']))
                                    <dd class="mt-0.5 overflow-x-auto rounded border border-slate-200 bg-white p-1.5">
                                        <code class="break-all text-xs text-slate-700">{{ $spf['dns_record'] }}</code>
                                    </dd>
                                @else
                                    <dd class="font-medium text-amber-600">{{ __('tools.email_header_analyzer.auth_dns_not_found') }}</dd>
                                @endif
                            </div>
                        @endif
                    </dl>
                @endif
            </div>

            {{-- DKIM --}}
            @php $dkim = $result['auth']['dkim'] ?? []; $dkimResult = $dkim['result'] ?? null; @endphp
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ __('tools.email_header_analyzer.auth_dkim') }}
                    </span>
                    <span class="rounded-full border px-3 py-0.5 text-xs font-bold {{ $authColor($dkimResult) }}">
                        {{ $dkimResult !== null ? strtoupper($dkimResult) : __('tools.email_header_analyzer.auth_none') }}
                    </span>
                </div>
                <dl class="space-y-1.5 text-xs">
                    @if (! empty($dkim['domain']))
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_domain') }}</dt>
                            <dd class="font-mono text-slate-700">{{ $dkim['domain'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($dkim['selector']))
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_selector') }}</dt>
                            <dd class="font-mono text-slate-700">{{ $dkim['selector'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($dkim['dns_name']))
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_dns_name') }}</dt>
                            <dd class="break-all font-mono text-slate-700">{{ $dkim['dns_name'] }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_dns_record') }}</dt>
                            @if (! empty($dkim['dns_record']))
                                <dd class="mt-0.5 overflow-x-auto rounded border border-slate-200 bg-white p-1.5">
                                    <code class="break-all text-xs text-slate-700">{{ $dkim['dns_record'] }}</code>
                                </dd>
                            @else
                                <dd class="font-medium text-amber-600">{{ __('tools.email_header_analyzer.auth_dns_not_found') }}</dd>
                            @endif
                        </div>
                    @endif
                </dl>
                @if (count($dkim['signatures'] ?? []) > 1)
                    <div class="mt-2 border-t border-slate-200 pt-2">
                        <p class="mb-1 text-xs font-semibold text-slate-400">{{ __('tools.email_header_analyzer.auth_signatures') }}</p>
                        @foreach ($dkim['signatures'] as $sig)
                            <p class="font-mono text-xs text-slate-600">
                                {{ $sig['selector'] ?? '?' }}<span class="text-slate-400">._domainkey.</span>{{ $sig['domain'] ?? '?' }}
                            </p>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- DMARC --}}
            @php $dmarc = $result['auth']['dmarc'] ?? []; $dmarcResult = $dmarc['result'] ?? null; @endphp
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        {{ __('tools.email_header_analyzer.auth_dmarc') }}
                    </span>
                    <span class="rounded-full border px-3 py-0.5 text-xs font-bold {{ $authColor($dmarcResult) }}">
                        {{ $dmarcResult !== null ? strtoupper($dmarcResult) : __('tools.email_header_analyzer.auth_none') }}
                    </span>
                </div>
                <dl class="space-y-1.5 text-xs">
                    @if (! empty($dmarc['domain']))
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_domain') }}</dt>
                            <dd class="font-mono text-slate-700">{{ $dmarc['domain'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($dmarc['policy']))
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_policy_label') }}</dt>
                            <dd class="font-mono text-slate-700">{{ $dmarc['policy'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($dmarc['dns_name']))
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_dns_name') }}</dt>
                            <dd class="break-all font-mono text-slate-700">{{ $dmarc['dns_name'] }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-500">{{ __('tools.email_header_analyzer.auth_dns_record') }}</dt>
                            @if (! empty($dmarc['dns_record']))
                                <dd class="mt-0.5 overflow-x-auto rounded border border-slate-200 bg-white p-1.5">
                                    <code class="break-all text-xs text-slate-700">{{ $dmarc['dns_record'] }}</code>
                                </dd>
                            @else
                                <dd class="font-medium text-amber-600">{{ __('tools.email_header_analyzer.auth_dns_not_found') }}</dd>
                            @endif
                        </div>
                    @endif
                </dl>
            </div>

        </div>{{-- end 3-col grid --}}

        @if (! empty($result['auth']['raw']))
            <details class="mt-3">
                <summary class="cursor-pointer text-xs text-slate-400 hover:text-slate-600">
                    {{ __('tools.email_header_analyzer.auth_raw') }}
                </summary>
                <pre class="mt-1 overflow-x-auto rounded bg-slate-50 p-2 font-mono text-xs text-slate-600 whitespace-pre-wrap break-all">{{ $result['auth']['raw'] }}</pre>
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
