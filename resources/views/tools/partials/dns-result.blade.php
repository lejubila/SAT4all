@if (! empty($validationErrors) && $validationErrors->isNotEmpty())
    <div class="rounded-lg border border-red-200 bg-red-50 p-5">
        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($validationErrors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@elseif (! empty($result))
    <div class="space-y-3">
        {{-- Header query --}}
        <div class="flex flex-wrap items-center gap-3">
            <span class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                <span class="text-slate-500">{{ __('tools.dns_lookup.result_host') }}: </span>
                <span class="font-mono font-semibold text-slate-800">{{ $result['host'] }}</span>
            </span>
            <span class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                <span class="rounded bg-sky-100 px-2 py-0.5 font-mono text-xs font-semibold text-sky-700">
                    {{ $result['type'] }}
                </span>
            </span>
            @if ($result['count'] > 0)
                <span class="text-sm text-slate-500">
                    {{ __('tools.dns_lookup.result_count', ['count' => $result['count']]) }}
                </span>
            @endif
        </div>

        @if ($result['count'] === 0)
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-center text-sm text-slate-500">
                {{ __('tools.dns_lookup.result_none') }}
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium">{{ __('tools.dns_lookup.col_type') }}</th>
                            <th class="px-4 py-2 text-left font-medium">{{ __('tools.dns_lookup.col_ttl') }}</th>
                            <th class="px-4 py-2 text-left font-medium">{{ __('tools.dns_lookup.col_data') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($result['records'] as $rec)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2">
                                    <span class="rounded bg-sky-100 px-2 py-0.5 font-mono text-xs font-semibold text-sky-700">
                                        {{ $rec['type'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-mono text-slate-500">{{ $rec['ttl'] }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $data = array_diff_key($rec, array_flip(['type', 'ttl']));
                                    @endphp
                                    @foreach ($data as $key => $val)
                                        <div class="flex gap-2 font-mono text-sm">
                                            <span class="shrink-0 text-slate-400">{{ $key }}:</span>
                                            <span class="break-all text-slate-800">{{ $val }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
