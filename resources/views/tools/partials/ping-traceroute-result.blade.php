@if (! empty($validationErrors) && $validationErrors->isNotEmpty())
    <div class="rounded-lg border border-red-200 bg-red-50 p-5">
        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($validationErrors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@elseif (! empty($result))
    @php
        $success = isset($result['exit_code']) && $result['exit_code'] === 0;
        $hasBinaryError = isset($result['error']);
    @endphp

    <div class="rounded-lg border {{ $success ? 'border-emerald-200' : 'border-red-200' }} bg-white shadow-sm">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b {{ $success ? 'border-emerald-100 bg-emerald-50' : 'border-red-100 bg-red-50' }} px-4 py-3">
            <div class="text-sm font-semibold {{ $success ? 'text-emerald-800' : 'text-red-800' }}">
                @if ($hasBinaryError)
                    {{ __('tools.ping_traceroute.error_binary_missing') }}
                @elseif ($success)
                    {{ __('tools.ping_traceroute.result_exit_ok') }}
                @else
                    {{ __('tools.ping_traceroute.result_exit_err', ['code' => $result['exit_code']]) }}
                @endif
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-0.5 font-mono text-xs text-slate-600">
                {{ strtoupper($result['tool']) }} {{ $result['target'] }}
            </span>
        </div>

        {{-- Output --}}
        @if (! $hasBinaryError && $result['output'] !== '')
            <pre class="overflow-x-auto whitespace-pre-wrap break-all px-4 py-4 font-mono text-xs leading-relaxed text-slate-700">{{ $result['output'] }}</pre>
        @endif
    </div>
@endif
