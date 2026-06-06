@extends('layouts.app')

@section('title', __('tools.cable_colors.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.cable_colors.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.cable_colors.description') }}
        </p>
    </section>

    {{-- Confronto T568A / T568B affiancato --}}
    <div class="grid gap-6 lg:grid-cols-2">
        @foreach (['t568a' => 'T568A', 't568b' => 'T568B'] as $stdKey => $stdLabel)
            <div class="rounded-lg border border-slate-200 shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                    <h2 class="text-base font-semibold text-slate-800">{{ $stdLabel }}</h2>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-center">{{ __('tools.cable_colors.col_pin') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('tools.cable_colors.col_color') }}</th>
                            <th class="px-3 py-2 text-center">{{ __('tools.cable_colors.col_pair') }}</th>
                            <th class="px-3 py-2 text-center">{{ __('tools.cable_colors.col_func_fast') }}</th>
                            <th class="px-3 py-2 text-center">{{ __('tools.cable_colors.col_func_gig') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($standards[$stdKey] as $pin)
                            @php $differs = in_array($pin['pin'], $differingPins, true); @endphp
                            <tr class="{{ $differs ? 'bg-amber-50' : 'hover:bg-slate-50' }}">
                                {{-- Numero pin --}}
                                <td class="px-3 py-2.5 text-center">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                                                 {{ $differs ? 'bg-amber-400 font-bold text-slate-900' : 'bg-slate-200 text-slate-700' }}
                                                 text-xs font-semibold">
                                        {{ $pin['pin'] }}
                                    </span>
                                </td>

                                {{-- Swatch + nome colore --}}
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        @if ($pin['stripe'])
                                            {{-- Filo a striscia: metà bianco, metà colore --}}
                                            <div class="flex h-5 w-8 shrink-0 overflow-hidden rounded border border-slate-300">
                                                <div class="w-1/2 bg-white"></div>
                                                <div class="w-1/2 {{ $pin['swatch'] }}"></div>
                                            </div>
                                        @else
                                            <div class="h-5 w-8 shrink-0 rounded border border-slate-300 {{ $pin['swatch'] }}"></div>
                                        @endif
                                        <span class="text-slate-700">{{ __('tools.cable_colors.' . $pin['name_key']) }}</span>
                                        @if ($differs)
                                            <span class="rounded bg-amber-200 px-1.5 py-0.5 text-xs font-medium text-amber-800">
                                                {{ __('tools.cable_colors.differs_badge') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Coppia --}}
                                <td class="px-3 py-2.5 text-center">
                                    <span class="rounded-full {{ $pin['swatch'] }} {{ $pin['text'] }} px-2 py-0.5 text-xs font-semibold">
                                        {{ $pin['pair'] }}
                                    </span>
                                </td>

                                {{-- Funzione 10/100 --}}
                                <td class="px-3 py-2.5 text-center font-mono text-xs font-semibold
                                           {{ $pin['func_fast'] !== '—' ? 'text-emerald-700' : 'text-slate-400' }}">
                                    {{ $pin['func_fast'] }}
                                </td>

                                {{-- Funzione Gigabit --}}
                                <td class="px-3 py-2.5 text-center font-mono text-xs text-slate-700">
                                    {{ $pin['func_gig'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>{{-- overflow-x-auto --}}
            </div>
        @endforeach
    </div>

    {{-- Note e legenda --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2">

        {{-- Nota differenze --}}
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <p class="font-semibold">{{ __('tools.cable_colors.note_diff') }}</p>
        </div>

        {{-- Nota uso pratico --}}
        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            {{ __('tools.cable_colors.note_usage') }}
        </div>
    </div>

    {{-- Legenda coppie --}}
    <div class="mt-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
            {{ __('tools.cable_colors.legend') }}
        </p>
        <div class="flex flex-wrap gap-3">
            @foreach ($pairs as $pair)
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded {{ $pair['swatch'] }}"></div>
                    <span class="text-sm text-slate-700">{{ __('tools.cable_colors.' . $pair['name_key']) }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
