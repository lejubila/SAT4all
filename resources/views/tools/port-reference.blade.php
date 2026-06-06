@extends('layouts.app')

@section('title', __('tools.port_reference.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.port_reference.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.port_reference.description') }}
        </p>
    </section>

    {{-- Filtri: ricerca live via htmx (GET, nessuna modifica di stato) --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input type="search" name="q" id="q"
               placeholder="{{ __('tools.port_reference.search_placeholder') }}"
               class="w-full flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm
                      focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
               hx-get="{{ route('tools.port-reference.lookup') }}"
               hx-target="#port-table"
               hx-swap="innerHTML"
               hx-trigger="keyup changed delay:300ms, search"
               hx-include="#protocol">

        <select name="protocol" id="protocol"
                class="rounded-md border border-slate-300 px-3 py-2 text-sm
                       focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                hx-get="{{ route('tools.port-reference.lookup') }}"
                hx-target="#port-table"
                hx-swap="innerHTML"
                hx-trigger="change"
                hx-include="#q">
            @foreach ($protocols as $proto)
                <option value="{{ $proto }}">{{ __('tools.port_reference.protocol_'.$proto) }}</option>
            @endforeach
        </select>
    </div>

    <div id="port-table">
        @include('tools.partials.port-reference-result', ['results' => $results])
    </div>
@endsection
