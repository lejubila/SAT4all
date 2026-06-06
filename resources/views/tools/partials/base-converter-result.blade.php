@php
    $idle    = $result['idle'] ?? false;
    $valid   = $result['valid'] ?? false;
    $error   = $result['error'] ?? null;
    $results = $result['results'] ?? [];
    $extras  = $result['extras'] ?? [];
    $errMsg  = $result['error_msg'] ?? null;

    $baseColors = [
        2  => ['bg' => 'bg-violet-50',  'border' => 'border-violet-200', 'badge' => 'bg-violet-100 text-violet-700', 'text' => 'text-violet-900'],
        8  => ['bg' => 'bg-amber-50',   'border' => 'border-amber-200',  'badge' => 'bg-amber-100 text-amber-700',   'text' => 'text-amber-900'],
        10 => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200','badge' => 'bg-emerald-100 text-emerald-700','text' => 'text-emerald-900'],
        16 => ['bg' => 'bg-blue-50',    'border' => 'border-blue-200',   'badge' => 'bg-blue-100 text-blue-700',     'text' => 'text-blue-900'],
    ];
@endphp

@if ($idle)
    <p class="text-sm text-slate-400">{{ __('tools.base_converter.idle') }}</p>

@elseif (! $valid)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        @if ($error === 'invalid_chars')
            {{ __('tools.base_converter.error_invalid_chars') }}
        @elseif ($error === 'overflow')
            {{ __('tools.base_converter.error_overflow') }}
        @else
            {{ $errMsg ?? __('tools.base_converter.error_invalid_chars') }}
        @endif
    </div>

@else
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($results as $r)
            @php $c = $baseColors[$r['base']] ?? $baseColors[10]; @endphp
            <div class="rounded-lg border {{ $c['border'] }} {{ $c['bg'] }} p-3">
                <div class="mb-2 flex items-center justify-between">
                    <span class="rounded px-2 py-0.5 text-xs font-bold {{ $c['badge'] }}">
                        {{ $r['label'] }}
                    </span>
                    <span class="text-xs text-slate-400">base {{ $r['base'] }}</span>
                </div>
                <p class="break-all font-mono text-sm font-semibold leading-relaxed {{ $c['text'] }}">
                    {{ $r['value'] }}
                </p>
            </div>
        @endforeach
    </div>

    @if (! empty($extras))
        <div class="mt-3 flex flex-wrap gap-4">
            @if (isset($extras['bit_length']))
                <div class="text-sm text-slate-600">
                    <span class="font-medium">{{ __('tools.base_converter.field_bit_length') }}:</span>
                    <span class="ml-1 font-mono">{{ $extras['bit_length'] }}</span>
                </div>
            @endif
            @if (isset($extras['ascii']))
                <div class="text-sm text-slate-600">
                    <span class="font-medium">{{ __('tools.base_converter.field_ascii') }}:</span>
                    <span class="ml-1 rounded bg-slate-100 px-2 py-0.5 font-mono text-slate-800">{{ $extras['ascii'] }}</span>
                </div>
            @endif
        </div>
    @endif
@endif
