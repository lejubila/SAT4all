@php
    $idle   = $result['idle'] ?? false;
    $valid  = $result['valid'] ?? false;
    $error  = $result['error'] ?? null;
    $detail = $result['error_detail'] ?? null;
    $fmt    = $result['format'] ?? '';
    $output = $result['output'] ?? '';
    $lines  = $result['lines'] ?? 0;
    $sizeIn = $result['size_in'] ?? 0;
    $sizeOut= $result['size_out'] ?? 0;

    $fmtColors = [
        'json' => 'bg-emerald-100 text-emerald-800',
        'xml'  => 'bg-blue-100 text-blue-800',
        'html' => 'bg-orange-100 text-orange-800',
    ];
    $fmtColor = $fmtColors[$fmt] ?? 'bg-slate-100 text-slate-700';

    $fmtLabel = strtoupper($fmt);

    $humanSize = fn(int $b): string => $b < 1024 ? "{$b} B" : number_format($b / 1024, 1) . ' KB';
@endphp

@if ($idle)
    <p class="text-sm text-slate-400">{{ __('tools.formatter.idle') }}</p>

@elseif (! $valid)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        @if ($error === 'too_large')
            {{ __('tools.formatter.error_too_large') }}
        @elseif ($error === 'unknown_format')
            {{ __('tools.formatter.error_unknown_format') }}
        @elseif ($error === 'invalid')
            {{ str_replace(':format', strtoupper($fmt ?: ''), __('tools.formatter.error_invalid')) }}
            @if ($detail)
                <span class="ml-1 font-mono text-xs opacity-80">— {{ $detail }}</span>
            @endif
        @else
            {{ $detail ?? __('tools.formatter.error_unknown_format') }}
        @endif
    </div>

@else
    {{-- Metadata bar --}}
    <div class="mb-3 flex flex-wrap items-center gap-3">
        <span class="rounded px-2 py-0.5 text-xs font-bold {{ $fmtColor }}">
            {{ $fmtLabel }}
        </span>
        <span class="text-xs text-slate-500">
            {{ __('tools.formatter.lines_count') }}: <strong>{{ $lines }}</strong>
        </span>
        <span class="text-xs text-slate-500">
            {{ __('tools.formatter.size_in') }}: <strong>{{ $humanSize($sizeIn) }}</strong>
            →
            {{ __('tools.formatter.size_out') }}: <strong>{{ $humanSize($sizeOut) }}</strong>
        </span>

        {{-- Copy button --}}
        <div class="ml-auto" x-data="{ copied: false }">
            <button type="button"
                    @click="navigator.clipboard.writeText(document.querySelector('#formatter-output').textContent).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                    :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded px-3 py-1 text-xs font-medium transition-colors">
                <span x-show="! copied">{{ __('tools.formatter.btn_copy') }}</span>
                <span x-show="copied" x-cloak>{{ __('tools.formatter.copied') }}</span>
            </button>
        </div>
    </div>

    {{-- Code block — Prism highlights this after htmx:afterSwap --}}
    <pre class="rounded-lg !m-0 overflow-x-auto text-sm"><code id="formatter-output" class="language-{{ $fmt }}">{{ $output }}</code></pre>
@endif
