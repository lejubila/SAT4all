@extends('layouts.app')

@section('title', __('tools.whois.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.whois.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.whois.description') }}
        </p>
    </section>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
         x-data="{ loading: false }"
         @htmx:before-request="loading = true"
         @htmx:after-request="loading = false">

        <form hx-post="{{ route('tools.whois.lookup') }}"
              hx-target="#whois-result"
              hx-swap="innerHTML">
            @csrf

            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="target" class="mb-1 block text-sm font-medium text-slate-700">
                        {{ __('tools.whois.input_target') }}
                    </label>
                    <input type="text"
                           id="target"
                           name="target"
                           placeholder="{{ __('tools.whois.placeholder_target') }}"
                           autocomplete="off"
                           x-data
                           @fill-ip.window="$el.value = $event.detail"
                           :disabled="loading"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm shadow-sm
                                  focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200
                                  disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                </div>

                <button type="submit"
                        :disabled="loading"
                        class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white
                               shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400
                               disabled:cursor-not-allowed disabled:opacity-60 transition-opacity">
                    <span x-show="!loading">{{ __('tools.whois.lookup') }}</span>
                    <span x-show="loading" x-cloak>{{ __('tools.whois.running') }}</span>
                </button>
            </div>
        </form>
    </div>

    <div id="whois-result" class="mt-6">
        @include('tools.partials.whois-result', ['errors' => null, 'result' => null])
    </div>
@endsection
