@if ($errors && $errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
        @foreach ($errors->all() as $error)
            <p class="text-sm text-red-700">{{ $error }}</p>
        @endforeach
    </div>

@elseif ($result === null)
    <p class="text-sm text-slate-400">{{ __('tools.whois.empty') }}</p>

@elseif (isset($result['error']) && $result['error'])
    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        {{ __('tools.whois.error_' . $result['error']) }}
    </div>

@else
    <div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
            <div>
                <span class="text-sm font-semibold text-slate-700">{{ __('tools.whois.result_title') }}</span>
                <span class="ml-2 font-mono text-sm text-emerald-700">{{ $target ?? '' }}</span>
            </div>
            @if ($result['truncated'] ?? false)
                <span class="text-xs text-amber-600">
                    {{ str_replace(':n', '300', __('tools.whois.truncated_notice')) }}
                </span>
            @endif
        </div>
        <div class="overflow-x-auto bg-slate-900 p-4">
            <pre class="whitespace-pre font-mono text-xs leading-relaxed text-slate-100">{{ implode("\n", $result['output']) }}</pre>
        </div>
    </div>
@endif
