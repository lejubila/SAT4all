@if (empty($results))
    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-center text-sm text-slate-500">
        {{ __('tools.port_reference.no_results') }}
    </div>
@else
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-2 text-xs text-slate-500">
            <span>{{ __('tools.port_reference.count', ['count' => count($results)]) }}</span>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-2 font-medium">{{ __('tools.port_reference.col_port') }}</th>
                    <th class="px-4 py-2 font-medium">{{ __('tools.port_reference.col_protocol') }}</th>
                    <th class="px-4 py-2 font-medium">{{ __('tools.port_reference.col_service') }}</th>
                    <th class="px-4 py-2 font-medium">{{ __('tools.port_reference.col_description') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($results as $entry)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono font-medium text-slate-800">{{ $entry['port'] }}</td>
                        <td class="px-4 py-2">
                            <span class="rounded px-1.5 py-0.5 text-xs font-medium uppercase
                                {{ match ($entry['protocol']) {
                                    'tcp' => 'bg-sky-100 text-sky-700',
                                    'udp' => 'bg-violet-100 text-violet-700',
                                    default => 'bg-slate-100 text-slate-600',
                                } }}">
                                {{ $entry['protocol'] }}
                            </span>
                        </td>
                        <td class="px-4 py-2 font-medium text-slate-700">{{ $entry['service'] }}</td>
                        <td class="px-4 py-2 text-slate-500">{{ $entry['description'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
