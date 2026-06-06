@php
    $connected = $result['connected'] ?? false;
    $error     = $result['error'] ?? null;
    $expired   = $result['expired'] ?? false;
    $expiring  = $result['expiring'] ?? false;
    $daysLeft  = $result['days_left'] ?? 0;
    $trusted   = $result['trusted'] ?? null;
    $cert      = $result['cert'] ?? [];
    $tls       = $result['tls'] ?? [];
    $host      = $result['host'] ?? '';
    $port      = $result['port'] ?? 443;
@endphp

@if (! $connected)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <span class="font-semibold">{{ __('tools.ssl_checker.status_error') }}:</span>
        {{ $error }}
    </div>
@else
    {{-- Status banner --}}
    @php
        if ($expired) {
            $bannerColor = 'border-red-200 bg-red-50 text-red-700';
            $statusLabel = __('tools.ssl_checker.status_expired');
            $icon = '✗';
        } elseif ($expiring) {
            $bannerColor = 'border-amber-200 bg-amber-50 text-amber-700';
            $statusLabel = __('tools.ssl_checker.status_expiring');
            $icon = '⚠';
        } else {
            $bannerColor = 'border-emerald-200 bg-emerald-50 text-emerald-700';
            $statusLabel = __('tools.ssl_checker.status_valid');
            $icon = '✓';
        }

        if ($daysLeft <= 0) {
            $daysLabel = __('tools.ssl_checker.days_left_zero');
        } elseif ($daysLeft === 1) {
            $daysLabel = __('tools.ssl_checker.days_left_one');
        } else {
            $daysLabel = str_replace(':n', $daysLeft, __('tools.ssl_checker.days_left_many'));
        }

        if ($trusted === true) {
            $trustLabel = __('tools.ssl_checker.status_trusted');
            $trustColor = 'bg-emerald-100 text-emerald-700';
        } elseif ($trusted === false) {
            $trustLabel = __('tools.ssl_checker.status_untrusted');
            $trustColor = 'bg-red-100 text-red-700';
        } else {
            $trustLabel = __('tools.ssl_checker.status_trust_unknown');
            $trustColor = 'bg-slate-100 text-slate-600';
        }
    @endphp

    <div class="mb-4 flex flex-wrap items-center gap-3 rounded-lg border {{ $bannerColor }} px-4 py-3">
        <span class="text-lg font-bold">{{ $icon }}</span>
        <span class="font-semibold">{{ $statusLabel }}</span>
        <span class="text-sm">{{ $host }}{{ $port !== 443 ? ':' . $port : '' }}</span>
        <span class="ml-auto rounded-full px-3 py-0.5 text-xs font-medium {{ $trustColor }}">{{ $trustLabel }}</span>
        @if (! $expired)
            <span class="rounded-full bg-white/60 px-3 py-0.5 text-xs font-medium">{{ $daysLabel }}</span>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

        {{-- Certificate details --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-2">
                <h3 class="text-sm font-semibold text-slate-700">{{ __('tools.ssl_checker.section_cert') }}</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-50">
                    @foreach ([
                        ['field_subject_cn', $cert['subject']['CN'] ?? '—'],
                        ['field_subject_o',  $cert['subject']['O']  ?? '—'],
                        ['field_issuer_cn',  $cert['issuer']['CN']  ?? '—'],
                        ['field_issuer_o',   $cert['issuer']['O']   ?? '—'],
                        ['field_valid_from', $cert['valid_from']    ?? '—'],
                        ['field_valid_to',   $cert['valid_to']      ?? '—'],
                        ['field_serial',     $cert['serial']        ?? '—'],
                    ] as [$key, $val])
                        <tr>
                            <td class="w-36 px-4 py-2 text-xs font-medium text-slate-500">{{ __('tools.ssl_checker.' . $key) }}</td>
                            <td class="px-4 py-2 font-mono text-xs text-slate-800 break-all">{{ $val }}</td>
                        </tr>
                    @endforeach
                    @if (! empty($cert['fingerprint']))
                        <tr>
                            <td class="w-36 px-4 py-2 text-xs font-medium text-slate-500">{{ __('tools.ssl_checker.field_fingerprint') }}</td>
                            <td class="px-4 py-2 font-mono text-xs text-slate-800 break-all">{{ $cert['fingerprint'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="space-y-4">

            {{-- TLS details --}}
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-2">
                    <h3 class="text-sm font-semibold text-slate-700">{{ __('tools.ssl_checker.section_tls') }}</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-50">
                        @foreach ([
                            ['field_protocol', $tls['protocol'] ?? '—'],
                            ['field_cipher',   $tls['cipher']   ?? '—'],
                            ['field_bits',     ($tls['bits'] ?? 0) ? $tls['bits'] . ' bit' : '—'],
                        ] as [$key, $val])
                            <tr>
                                <td class="w-28 px-4 py-2 text-xs font-medium text-slate-500">{{ __('tools.ssl_checker.' . $key) }}</td>
                                <td class="px-4 py-2 font-mono text-xs text-slate-800">{{ $val }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- SANs --}}
            @if (! empty($cert['sans']))
                <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-4 py-2">
                        <h3 class="text-sm font-semibold text-slate-700">
                            {{ __('tools.ssl_checker.section_sans') }}
                            <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">{{ count($cert['sans']) }}</span>
                        </h3>
                    </div>
                    <div class="flex flex-wrap gap-1.5 px-4 py-3">
                        @foreach ($cert['sans'] as $san)
                            <span class="rounded bg-slate-100 px-2 py-0.5 font-mono text-xs text-slate-700">{{ $san }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
@endif
