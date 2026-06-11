@extends('layouts.app')

@section('title', __('tools.email_header_analyzer.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.email_header_analyzer.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.email_header_analyzer.description') }}
        </p>
    </section>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Input --}}
        <div class="lg:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">
                {{ __('tools.email_header_analyzer.label_input') }}
            </label>
            <textarea name="header"
                      rows="22"
                      placeholder="{{ __('tools.email_header_analyzer.placeholder_input') }}"
                      hx-post="{{ route('tools.email-header-analyzer.analyze') }}"
                      hx-target="#result"
                      hx-swap="innerHTML"
                      hx-trigger="input changed delay:400ms, keyup[key=='Enter'] changed"
                      hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-xs leading-relaxed
                             shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200
                             resize-y"></textarea>

            <form hx-post="{{ route('tools.email-header-analyzer.analyze') }}"
                  hx-target="#result"
                  hx-swap="innerHTML"
                  hx-include="[name='header']"
                  class="mt-3">
                @csrf
                <button type="submit"
                        class="w-full rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white
                               shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    {{ __('tools.email_header_analyzer.btn_analyze') }}
                </button>
            </form>
        </div>

        {{-- Result --}}
        <div id="result" class="lg:col-span-3">
            <p class="text-sm text-slate-400">{{ __('tools.email_header_analyzer.empty') }}</p>
        </div>
    </div>
@endsection
