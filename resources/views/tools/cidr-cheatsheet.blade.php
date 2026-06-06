@extends('layouts.app')

@section('title', __('tools.cidr_cheatsheet.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.cidr_cheatsheet.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.cidr_cheatsheet.description') }}
        </p>
    </section>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-800 text-xs uppercase tracking-wide text-slate-100">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">{{ __('tools.cidr_cheatsheet.col_cidr') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('tools.cidr_cheatsheet.col_netmask') }}</th>
                    <th class="hidden px-4 py-3 text-left font-medium sm:table-cell">{{ __('tools.cidr_cheatsheet.col_wildcard') }}</th>
                    <th class="px-4 py-3 text-right font-medium">{{ __('tools.cidr_cheatsheet.col_total') }}</th>
                    <th class="px-4 py-3 text-right font-medium">{{ __('tools.cidr_cheatsheet.col_usable') }}</th>
                    <th class="hidden px-4 py-3 text-left font-medium lg:table-cell">{{ __('tools.cidr_cheatsheet.col_note') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($rows as $row)
                    @php
                        $highlight = in_array($row['cidr'], [8, 16, 24], true);
                        $muted     = $row['cidr'] < 8;
                    @endphp
                    <tr class="
                        {{ $highlight ? 'bg-emerald-50 font-semibold' : ($muted ? 'text-slate-400' : 'hover:bg-slate-50') }}
                    ">
                        <td class="px-4 py-2 font-mono text-slate-800">/{{ $row['cidr'] }}</td>
                        <td class="px-4 py-2 font-mono text-slate-700">{{ $row['netmask'] }}</td>
                        <td class="hidden px-4 py-2 font-mono text-slate-500 sm:table-cell">{{ $row['wildcard'] }}</td>
                        <td class="px-4 py-2 text-right font-mono text-slate-600">{{ $row['total'] }}</td>
                        <td class="px-4 py-2 text-right font-mono text-slate-800">{{ $row['usable'] }}</td>
                        <td class="hidden px-4 py-2 lg:table-cell">
                            @if ($row['note'])
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                                    {{ $row['note'] }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
