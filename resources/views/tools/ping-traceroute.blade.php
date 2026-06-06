@extends('layouts.app')

@section('title', __('tools.ping_traceroute.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.ping_traceroute.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.ping_traceroute.description') }}
        </p>
    </section>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Form --}}
        <form class="lg:col-span-2 rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-4"
              x-data="{ tool: 'ping', loading: false }"
              @htmx:before-request="loading = true"
              @htmx:after-request="loading = false"
              hx-post="{{ route('tools.ping-traceroute.run') }}"
              hx-target="#result"
              hx-swap="innerHTML"
              hx-indicator="#spinner">
            @csrf

            {{-- Target --}}
            <div>
                <label for="target" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ping_traceroute.input_target') }}
                </label>
                <input type="text" name="target" id="target"
                       value="8.8.8.8"
                       placeholder="{{ __('tools.ping_traceroute.placeholder_target') }}"
                       :disabled="loading"
                       @fill-ip.window="$el.value = $event.detail"
                       class="w-full rounded-md border border-slate-300 px-3 py-2 font-mono text-sm
                              focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500
                              disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
            </div>

            {{-- Selettore strumento --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ping_traceroute.input_tool') }}
                </label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 rounded-md border px-4 py-2 text-sm transition"
                           :class="loading ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'"
                           :class="tool === 'ping' ? 'border-emerald-500 bg-emerald-50 font-semibold text-emerald-700' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="tool" value="ping" x-model="tool" :disabled="loading" class="hidden">
                        {{ __('tools.ping_traceroute.tool_ping') }}
                    </label>
                    <label class="flex items-center gap-2 rounded-md border px-4 py-2 text-sm transition"
                           :class="loading ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'"
                           :class="tool === 'traceroute' ? 'border-emerald-500 bg-emerald-50 font-semibold text-emerald-700' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="tool" value="traceroute" x-model="tool" :disabled="loading" class="hidden">
                        {{ __('tools.ping_traceroute.tool_traceroute') }}
                    </label>
                </div>
            </div>

            {{-- Opzioni ping --}}
            <div x-show="tool === 'ping'" x-cloak>
                <label for="count" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ping_traceroute.input_count') }}
                </label>
                <select name="count" id="count"
                        :disabled="loading"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm
                               focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500
                               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                    @foreach ([1, 2, 3, 4, 5, 10] as $n)
                        <option value="{{ $n }}" @selected($n === 4)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Opzioni traceroute --}}
            <div x-show="tool === 'traceroute'" x-cloak>
                <label for="hops" class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.ping_traceroute.input_hops') }}
                </label>
                <select name="hops" id="hops"
                        :disabled="loading"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm
                               focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500
                               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                    @foreach ([5, 10, 15, 20, 30] as $n)
                        <option value="{{ $n }}" @selected($n === 20)>{{ $n }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-amber-600">
                    {{ __('tools.ping_traceroute.note_traceroute') }}
                </p>
            </div>

            <button type="submit"
                    :disabled="loading"
                    class="w-full rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900
                           hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500
                           disabled:cursor-not-allowed disabled:opacity-50">
                <span id="spinner" class="htmx-indicator mr-1">⟳</span>
                <span x-text="loading ? '{{ __('tools.ping_traceroute.running') }}' : '{{ __('tools.ping_traceroute.run') }}'"></span>
            </button>
        </form>

        {{-- Risultato --}}
        <div id="result" class="lg:col-span-3">
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500">
                {{ __('tools.ping_traceroute.empty') }}
            </div>
        </div>
    </div>
@endsection
