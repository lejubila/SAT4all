@extends('layouts.app')

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

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Form --}}
        <form class="lg:col-span-2 rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-4"
              hx-post="{{ route('tools.dns-lookup.lookup') }}"
              hx-target="#result"
              hx-swap="innerHTML"
              hx-indicator="#spinner">
            @csrf

            <div>
                <label for="host" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.dns_lookup.input_host') }}
                </label>
                <input type="text" name="host" id="host"
                       value="example.com"
                       placeholder="{{ __('tools.dns_lookup.placeholder_host') }}"
                       x-data
                       @fill-ip.window="$el.value = $event.detail"
                       class="w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm
                              focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label for="type" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.dns_lookup.input_type') }}
                </label>
                <select name="type" id="type"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm
                               focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @foreach ($recordTypes as $rt)
                        <option value="{{ $rt }}" @selected($rt === 'A')>{{ $rt }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900
                           hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span id="spinner" class="htmx-indicator mr-1">⟳</span>
                {{ __('tools.dns_lookup.lookup') }}
            </button>
        </form>

        {{-- Risultato --}}
        <div id="result" class="lg:col-span-3">
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500">
                {{ __('tools.dns_lookup.empty') }}
            </div>
        </div>
    </div>
@endsection
