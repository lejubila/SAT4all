@extends('layouts.app')

@section('title', __('tools.osi_model.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.osi_model.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.osi_model.description') }}
        </p>
    </section>

    {{-- Visualizzazione a stack (dall'Application al Physical) --}}
    <div class="space-y-3">
        @foreach ($layers as $layer)
            @php
                $c = $colorClasses[$layer['color']];
                $nameKey = "tools.osi_model.layer_{$layer['number']}_{$layer['key']}_name";
                $descKey = "tools.osi_model.layer_{$layer['number']}_{$layer['key']}_desc";
            @endphp

            <div class="overflow-hidden rounded-lg border {{ $c['border'] }} bg-white shadow-sm">
                {{-- Header del layer --}}
                <div class="flex items-center gap-4 px-4 py-3">
                    {{-- Numero layer --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $c['num_bg'] }} {{ $c['num_text'] }} text-lg font-extrabold shadow">
                        {{ $layer['number'] }}
                    </div>

                    {{-- Nome --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            {{ __('tools.osi_model.col_layer') }} {{ $layer['number'] }}
                        </p>
                        <p class="text-lg font-bold text-slate-800">
                            {{ __($nameKey) }}
                        </p>
                    </div>

                    {{-- PDU badge --}}
                    <span class="shrink-0 rounded-full {{ $c['badge'] }} px-3 py-1 text-xs font-semibold">
                        {{ $layer['pdu'] }}
                    </span>
                </div>

                {{-- Dettagli espandibili con Alpine --}}
                <div x-data="{ open: false }" class="border-t {{ $c['border'] }}">
                    <button type="button"
                            @click="open = !open"
                            class="flex w-full items-center justify-between bg-slate-50 px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            {{ __('tools.osi_model.col_desc') }} · {{ __('tools.osi_model.col_protocols') }} · {{ __('tools.osi_model.col_devices') }}
                        </span>
                        <span class="ml-2 text-slate-400" x-text="open ? '▲' : '▼'"></span>
                    </button>

                    <div x-show="open" x-cloak x-transition class="px-4 py-4 text-sm">
                        {{-- Descrizione --}}
                        <p class="mb-4 text-slate-600">{{ __($descKey) }}</p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            {{-- Protocolli --}}
                            <div>
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {{ __('tools.osi_model.col_protocols') }}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($layer['protocols'] as $proto)
                                        <span class="rounded {{ $c['badge'] }} px-2 py-0.5 text-xs font-medium">
                                            {{ $proto }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Dispositivi --}}
                            <div>
                                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {{ __('tools.osi_model.col_devices') }}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($layer['devices'] as $device)
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                            {{ $device }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
