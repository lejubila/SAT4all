@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.3/css/flag-icons.min.css">
<style>
    .fi { width: 1.5em; border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,.08); }

    /* Spinner SVG che ruota */
    .spin { animation: spin-anim 0.8s linear infinite; }
    @keyframes spin-anim { to { transform: rotate(360deg); } }

    /* htmx-indicator: nascosto di default, visibile durante request */
    .htmx-indicator { display: none; }
    .htmx-request .htmx-indicator { display: inline-flex; }
    .htmx-request.htmx-indicator  { display: inline-flex; }
</style>
@endpush

@section('title', __('tools.dns_lookup.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.dns_lookup.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.dns_lookup.description') }}
        </p>
    </section>

    {{-- Template nascosto per il loading skeleton --}}
    <template id="tpl-loading">
        <div class="rounded-lg border border-slate-200 bg-white p-10 flex flex-col items-center justify-center gap-4 text-slate-500 min-h-[180px]">
            <svg class="spin h-10 w-10 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <p class="text-sm font-medium text-slate-600">{{ __('tools.dns_lookup.loading') }}</p>
        </div>
    </template>

    <div x-data="{ mode: 'single' }" class="grid gap-6 lg:grid-cols-5">

        {{-- Form --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Mode tabs --}}
            <div class="flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm gap-1">
                <button type="button"
                        @click="mode = 'single'; document.getElementById('result').innerHTML = ''"
                        :class="mode === 'single'
                            ? 'bg-emerald-500 text-slate-900 font-semibold'
                            : 'text-slate-600 hover:bg-slate-100'"
                        class="flex-1 rounded-md px-3 py-1.5 text-sm transition-colors">
                    {{ __('tools.dns_lookup.tab_single') }}
                </button>
                <button type="button"
                        @click="mode = 'propagation'; document.getElementById('result').innerHTML = ''"
                        :class="mode === 'propagation'
                            ? 'bg-emerald-500 text-slate-900 font-semibold'
                            : 'text-slate-600 hover:bg-slate-100'"
                        class="flex-1 rounded-md px-3 py-1.5 text-sm transition-colors">
                    {{ __('tools.dns_lookup.tab_propagation') }}
                </button>
            </div>

            {{-- Single-query form --}}
            <form x-show="mode === 'single'"
                  class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-4"
                  hx-post="{{ route('tools.dns-lookup.lookup') }}"
                  hx-target="#result"
                  hx-swap="innerHTML"
                  hx-on:htmx:before-request="document.getElementById('result').innerHTML = document.getElementById('tpl-loading').innerHTML">
                @csrf

                <div>
                    <label for="host-single" class="mb-1 block text-sm font-medium text-slate-700">
                        {{ __('tools.dns_lookup.input_host') }}
                    </label>
                    <input type="text" name="host" id="host-single"
                           value="example.com"
                           placeholder="{{ __('tools.dns_lookup.placeholder_host') }}"
                           x-data
                           @fill-ip.window="$el.value = $event.detail"
                           class="w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm
                                  focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="type-single" class="mb-1 block text-sm font-medium text-slate-700">
                        {{ __('tools.dns_lookup.input_type') }}
                    </label>
                    <select name="type" id="type-single"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm
                                   focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        @foreach ($recordTypes as $rt)
                            <option value="{{ $rt }}" @selected($rt === 'A')>{{ $rt }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="relative w-full rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900
                               hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500
                               flex items-center justify-center gap-2">
                    {{-- Spinner visibile solo durante la request --}}
                    <span id="spinner-single" class="htmx-indicator items-center gap-1.5 text-slate-900">
                        <svg class="spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                    {{ __('tools.dns_lookup.lookup') }}
                </button>
            </form>

            {{-- Propagation form --}}
            <form x-show="mode === 'propagation'"
                  class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-4"
                  hx-post="{{ route('tools.dns-lookup.propagation') }}"
                  hx-target="#result"
                  hx-swap="innerHTML"
                  hx-on:htmx:before-request="document.getElementById('result').innerHTML = document.getElementById('tpl-loading').innerHTML">
                @csrf

                <p class="text-xs text-slate-500">
                    {{ __('tools.dns_lookup.prop_description') }}
                </p>

                <div>
                    <label for="host-prop" class="mb-1 block text-sm font-medium text-slate-700">
                        {{ __('tools.dns_lookup.input_host') }}
                    </label>
                    <input type="text" name="host" id="host-prop"
                           value="example.com"
                           placeholder="{{ __('tools.dns_lookup.placeholder_host') }}"
                           x-data
                           @fill-ip.window="$el.value = $event.detail"
                           class="w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm
                                  focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="type-prop" class="mb-1 block text-sm font-medium text-slate-700">
                        {{ __('tools.dns_lookup.input_type') }}
                    </label>
                    <select name="type" id="type-prop"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm
                                   focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        @foreach ($propagationTypes as $rt)
                            <option value="{{ $rt }}" @selected($rt === 'A')>{{ $rt }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="relative w-full rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900
                               hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500
                               flex items-center justify-center gap-2">
                    {{-- Spinner visibile solo durante la request --}}
                    <span id="spinner-prop" class="htmx-indicator items-center gap-1.5 text-slate-900">
                        <svg class="spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                    {{ __('tools.dns_lookup.prop_button') }}
                </button>
            </form>
        </div>

        {{-- Result area --}}
        <div id="result" class="lg:col-span-3">
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500">
                {{ __('tools.dns_lookup.empty') }}
            </div>
        </div>
    </div>
@endsection
