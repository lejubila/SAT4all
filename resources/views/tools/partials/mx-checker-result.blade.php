{{-- OOB: aggiorna il codice captcha senza ricaricare la pagina --}}
<div id="mx-checker-captcha"
     hx-swap-oob="true"
     class="inline-block select-none rounded-lg border border-slate-300 bg-slate-100 px-6 py-3
            font-mono text-2xl font-bold tracking-[0.4em] text-emerald-700 shadow-inner">
    {{ $newCaptcha ?? '' }}
</div>

@php
    $formatSize = function (?int $bytes): ?string {
        if ($bytes === null) return null;
        if ($bytes >= 1048576) return round($bytes / 1048576, 0) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
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

    {{-- Heading --}}
    <div class="mb-5">
        @if ($result['mx_count'] === 0)
            <p class="text-sm text-slate-500">
                {{ __('tools.mx_checker.no_mx') }}
                <span class="font-mono font-semibold text-slate-700">{{ $result['domain'] }}</span>
            </p>
        @else
            <p class="text-sm text-slate-500">
                <span class="font-semibold text-slate-800">{{ $result['mx_count'] }}</span>
                {{ __('tools.mx_checker.server_count', ['domain' => $result['domain']]) }}
            </p>
        @endif
    </div>

    {{-- Cards --}}
    <div class="space-y-4">
        @foreach ($result['servers'] as $server)
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">

                {{-- Card header --}}
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="rounded bg-slate-200 px-2 py-0.5 text-xs font-semibold text-slate-600">
                            {{ __('tools.mx_checker.priority') }} {{ $server['priority'] }}
                        </span>
                        <span class="font-mono font-semibold text-slate-800">{{ $server['host'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($server['smtp']['reachable'])
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                {{ __('tools.mx_checker.reachable') }}
                            </span>
                            @if ($server['smtp']['latency_ms'] !== null)
                                <span class="text-xs text-slate-400">{{ $server['smtp']['latency_ms'] }} ms</span>
                            @endif
                        @else
                            <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                                {{ __('tools.mx_checker.unreachable') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="px-4 py-3 space-y-3">

                    {{-- IPs --}}
                    @if (! empty($server['ips']))
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500">IP</span>
                            @foreach ($server['ips'] as $ip)
                                <span class="rounded bg-slate-100 px-2 py-0.5 font-mono text-xs text-slate-700">{{ $ip }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400">{{ __('tools.mx_checker.ip_unresolvable') }}</p>
                    @endif

                    @if ($server['smtp']['reachable'])

                        {{-- Banner --}}
                        @if ($server['smtp']['banner'])
                            <div>
                                <span class="text-xs font-semibold text-slate-500">{{ __('tools.mx_checker.label_banner') }}</span>
                                <code class="ml-2 break-all rounded bg-slate-50 px-2 py-0.5 text-xs text-slate-700">
                                    {{ $server['smtp']['banner'] }}
                                </code>
                            </div>
                        @endif

                        {{-- Capabilities --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500">{{ __('tools.mx_checker.label_capabilities') }}</span>

                            @if ($server['smtp']['starttls'])
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">STARTTLS</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-500">no STARTTLS</span>
                            @endif

                            @if ($server['smtp']['auth'])
                                <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                    AUTH: {{ $server['smtp']['auth'] }}
                                </span>
                            @endif

                            @if ($server['smtp']['size'])
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">
                                    SIZE: {{ $formatSize($server['smtp']['size']) }}
                                </span>
                            @endif

                            @if (in_array('8BITMIME', $server['smtp']['ehlo_lines'], true))
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">8BITMIME</span>
                            @endif
                        </div>

                        {{-- All EHLO lines (collapsible) --}}
                        @if (! empty($server['smtp']['ehlo_lines']))
                            <details class="text-xs">
                                <summary class="cursor-pointer text-slate-400 hover:text-slate-600">
                                    {{ __('tools.mx_checker.show_ehlo') }} ({{ count($server['smtp']['ehlo_lines']) }})
                                </summary>
                                <div class="mt-1 rounded bg-slate-50 p-2 font-mono">
                                    @foreach ($server['smtp']['ehlo_lines'] as $line)
                                        <div class="text-slate-600">250{{ ! $loop->last ? '-' : ' ' }} {{ $line }}</div>
                                    @endforeach
                                </div>
                            </details>
                        @endif

                    @else
                        {{-- Unreachable note --}}
                        <p class="text-xs text-amber-700">
                            {{ __('tools.mx_checker.port25_blocked') }}
                        </p>
                    @endif

                </div>
            </div>
        @endforeach
    </div>

@endif
