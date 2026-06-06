@extends('layouts.app')

@section('title', __('tools.ipv6_calculator.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.ipv6_calculator.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.ipv6_calculator.description') }}
        </p>
    </section>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Form --}}
        <form class="lg:col-span-2 rounded-lg border border-slate-200 bg-white p-5 shadow-sm"
              hx-post="{{ route('tools.ipv6-calculator.calculate') }}"
              hx-target="#result"
              hx-swap="innerHTML">
            @csrf

            <div class="mb-4">
                <label for="address" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ipv6_calculator.input_address') }}
                </label>
                <input type="text" name="address" id="address"
                       value="2001:db8::1"
                       placeholder="{{ __('tools.ipv6_calculator.placeholder_address') }}"
                       class="w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm
                              focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="mb-4">
                <label for="prefix" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ipv6_calculator.input_prefix') }}
                </label>
                <select name="prefix" id="prefix"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm
                               focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @foreach ($prefixOptions as $p)
                        <option value="{{ $p }}" @selected($p === 64)>/{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900
                           hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                {{ __('tools.ipv6_calculator.calculate') }}
            </button>
        </form>

        {{-- Risultato (riempito da htmx) --}}
        <div id="result" class="lg:col-span-3">
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500">
                {{ __('tools.ipv6_calculator.empty') }}
            </div>
        </div>
    </div>
@endsection
