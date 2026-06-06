@extends('layouts.app')

@section('title', __('tools.cable_schemas.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.cable_schemas.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.cable_schemas.description') }}
        </p>
    </section>

    {{-- ── Standard T568A / T568B side-by-side ── --}}
    <section class="mb-10">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">
            {{ __('tools.cable_schemas.section_standards') }}
        </h2>

        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($standards as $name => $pins)
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50 px-4 py-2">
                        <span class="font-semibold text-slate-800">{{ $name }}</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <th class="w-12 px-4 py-2 text-center font-medium">{{ __('tools.cable_schemas.col_pin') }}</th>
                                <th class="px-4 py-2 font-medium">{{ __('tools.cable_schemas.col_color') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pins as $i => $wire)
                                <tr>
                                    <td class="px-4 py-2 text-center font-mono font-semibold text-slate-700">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-4 w-8 shrink-0 rounded border {{ $wire['tw'] }}"></span>
                                            <span class="text-slate-600">{{ $wire['label'] }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ── Tipi di cavo con tab Alpine.js ── --}}
    <section x-data="{ active: 'straight' }">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">
            {{ __('tools.cable_schemas.section_cables') }}
        </h2>

        {{-- Tab bar --}}
        <div class="mb-4 flex gap-2 border-b border-slate-200">
            @foreach (array_keys($cables) as $key)
                <button type="button"
                        @click="active = '{{ $key }}'"
                        :class="active === '{{ $key }}'
                            ? 'border-b-2 border-emerald-500 text-emerald-700 font-semibold'
                            : 'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2 text-sm -mb-px">
                    {{ __('tools.cable_schemas.cable_'.$key) }}
                </button>
            @endforeach
        </div>

        {{-- Tab panels --}}
        @foreach ($cables as $key => $cable)
            @php
                $pinsA = $standards[$cable['standard_a']];
                $pinsB = $standards[$cable['standard_b']];
            @endphp

            <div x-show="active === '{{ $key }}'" x-cloak>
                <p class="mb-3 text-sm text-slate-500">
                    <span class="font-medium text-slate-700">{{ __('tools.cable_schemas.cable_'.$key) }}:</span>
                    {{ __('tools.cable_schemas.cable_'.$key.'_use') }}
                </p>

                <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-2 text-center font-medium">{{ __('tools.cable_schemas.col_pin') }}</th>
                                <th class="px-4 py-2 font-medium">{{ __('tools.cable_schemas.end_a') }} ({{ $cable['standard_a'] }})</th>
                                <th class="w-8 px-2 py-2 text-center font-medium">→</th>
                                <th class="px-4 py-2 font-medium">{{ __('tools.cable_schemas.end_b') }} ({{ $cable['standard_b'] }})</th>
                                <th class="px-4 py-2 text-center font-medium">{{ __('tools.cable_schemas.col_pin') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($cable['map'] as $pinIdx => $destPin)
                                @php
                                    $wireA = $pinsA[$pinIdx];
                                    $wireB = $pinsB[$destPin - 1];
                                    $isCrossed = ($pinIdx + 1) !== $destPin;
                                @endphp
                                <tr class="{{ $isCrossed ? 'bg-amber-50' : '' }}">
                                    <td class="px-4 py-2 text-center font-mono font-semibold text-slate-700">{{ $pinIdx + 1 }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-4 w-8 shrink-0 rounded border {{ $wireA['tw'] }}"></span>
                                            <span class="text-slate-600">{{ $wireA['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 text-center text-slate-400">
                                        {{ $isCrossed ? '⇄' : '→' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-4 w-8 shrink-0 rounded border {{ $wireB['tw'] }}"></span>
                                            <span class="text-slate-600">{{ $wireB['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-center font-mono font-semibold text-slate-700">{{ $destPin }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- Nota Auto-MDI/MDI-X --}}
        <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            ℹ {{ __('tools.cable_schemas.note_mdix') }}
        </p>
    </section>
@endsection
