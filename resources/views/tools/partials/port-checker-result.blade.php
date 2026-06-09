@php
    $errors     = $errors ?? null;
    $result     = $result ?? null;
    $newCaptcha = $newCaptcha ?? '';

    $statusConfig = [
        'open'         => ['label' => __('tools.port_checker.status_open'),          'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'dot' => 'bg-emerald-500'],
        'closed'       => ['label' => __('tools.port_checker.status_closed'),         'bg' => 'bg-red-100',     'text' => 'text-red-800',     'dot' => 'bg-red-500'],
        'filtered'     => ['label' => __('tools.port_checker.status_filtered'),       'bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'dot' => 'bg-amber-500'],
        'open_filtered'=> ['label' => __('tools.port_checker.status_open_filtered'),  'bg' => 'bg-orange-100',  'text' => 'text-orange-800',  'dot' => 'bg-orange-500'],
    ];

    $status = $result['status'] ?? null;
    $cfg    = $statusConfig[$status] ?? null;
@endphp

{{-- OOB: aggiorna il codice captcha senza ricaricare la pagina --}}
<div id="port-checker-captcha"
     hx-swap-oob="true"
     class="inline-block select-none rounded-lg border border-slate-300 bg-slate-100 px-6 py-3 font-mono text-2xl font-bold tracking-[0.4em] text-emerald-700 shadow-inner">
    {{ $newCaptcha }}
</div>

{{-- Errori di validazione --}}
@if ($errors && $errors->isNotEmpty())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>

{{-- Risultato --}}
@elseif ($result && $cfg)
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        {{-- Badge stato --}}
        <div class="mb-5 flex items-center gap-3">
            <span class="flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                <span class="h-2 w-2 rounded-full {{ $cfg['dot'] }}"></span>
                {{ $cfg['label'] }}
            </span>
        </div>

        {{-- Dettagli --}}
        <dl class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:grid-cols-4">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('tools.port_checker.result_host') }}</dt>
                <dd class="mt-1 font-mono text-slate-800">{{ $result['host'] }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('tools.port_checker.result_port') }}</dt>
                <dd class="mt-1 font-mono text-slate-800">{{ $result['port'] }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('tools.port_checker.result_protocol') }}</dt>
                <dd class="mt-1 font-mono text-slate-800">{{ $result['protocol'] }}</dd>
            </div>
            @if ($result['latency_ms'] !== null)
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('tools.port_checker.result_latency') }}</dt>
                    <dd class="mt-1 font-mono text-slate-800">
                        {{ str_replace(':ms', $result['latency_ms'], __('tools.port_checker.result_latency_ms')) }}
                    </dd>
                </div>
            @endif
        </dl>

        {{-- Nota UDP --}}
        @if (! empty($result['udp_note']))
            <p class="mt-4 text-xs text-slate-500">
                {{ __('tools.port_checker.udp_note') }}
            </p>
        @endif
    </div>
@endif
