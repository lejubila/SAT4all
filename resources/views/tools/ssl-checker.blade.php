@extends('layouts.app')

@section('title', __('tools.ssl_checker.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.ssl_checker.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.ssl_checker.description') }}
        </p>
    </section>

    <form hx-post="{{ route('tools.ssl-checker.check') }}"
          hx-target="#result"
          hx-swap="innerHTML"
          hx-indicator="#spinner"
          class="mb-6">
        @csrf

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ssl_checker.label_host') }}
                </label>
                <input type="text"
                       name="host"
                       placeholder="{{ __('tools.ssl_checker.placeholder_host') }}"
                       autocomplete="off"
                       spellcheck="false"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <div class="w-32">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ssl_checker.label_port') }}
                </label>
                <input type="number"
                       name="port"
                       value="443"
                       min="1"
                       max="65535"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <button type="submit"
                    class="flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <span id="spinner" class="htmx-indicator">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </span>
                {{ __('tools.ssl_checker.btn_check') }}
            </button>
        </div>
    </form>

    <div id="result">
        <p class="text-sm text-slate-400">{{ __('tools.ssl_checker.empty') }}</p>
    </div>
@endsection
