@extends('layouts.app')

@section('title', __('tools.mx_checker.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.mx_checker.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.mx_checker.description') }}
        </p>
    </section>

    <form hx-post="{{ route('tools.mx-checker.check') }}"
          hx-target="#result"
          hx-swap="innerHTML"
          hx-indicator="#spinner"
          class="mb-6 space-y-4">
        @csrf

        {{-- Domain --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.mx_checker.label_domain') }}
                </label>
                <input type="text"
                       name="domain"
                       placeholder="{{ __('tools.mx_checker.placeholder_domain') }}"
                       autocomplete="off"
                       spellcheck="false"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm
                              focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>
        </div>

        {{-- Captcha --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div>
                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                    {{ __('tools.mx_checker.label_captcha') }}
                </p>
                <div id="mx-checker-captcha"
                     class="inline-block select-none rounded-lg border border-slate-300 bg-slate-100 px-6 py-3
                            font-mono text-2xl font-bold tracking-[0.4em] text-emerald-700 shadow-inner">
                    {{ $captcha }}
                </div>
            </div>

            <div class="flex-1 sm:max-w-xs">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.mx_checker.label_captcha') }}
                </label>
                <input type="text"
                       name="captcha_input"
                       autocomplete="off"
                       autocorrect="off"
                       autocapitalize="characters"
                       spellcheck="false"
                       maxlength="6"
                       placeholder="{{ __('tools.mx_checker.placeholder_captcha') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm uppercase
                              shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <button type="submit"
                    class="flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold
                           text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <span id="spinner" class="htmx-indicator">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </span>
                {{ __('tools.mx_checker.btn_check') }}
            </button>
        </div>

        <p class="text-xs text-slate-400">{{ __('tools.mx_checker.hint') }}</p>
    </form>

    <div id="result">
        <p class="text-sm text-slate-400">{{ __('tools.mx_checker.empty') }}</p>
    </div>
@endsection
