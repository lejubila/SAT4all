@extends('layouts.app')

@section('title', __('tools.base_converter.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.base_converter.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.base_converter.description') }}
        </p>
    </section>

    <div x-data="baseConverter">

        <form hx-post="{{ route('tools.base-converter.convert') }}"
              hx-target="#result"
              hx-swap="innerHTML"
              hx-trigger="input delay:300ms, submit"
              class="space-y-4">
            @csrf
            <input type="hidden" name="from_base" :value="fromBase">

            {{-- Source base selector --}}
            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">
                    {{ __('tools.base_converter.label_from_base') }}
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach ([2 => 'BIN', 8 => 'OCT', 10 => 'DEC', 16 => 'HEX'] as $b => $label)
                        <button type="button"
                                @click="fromBase = {{ $b }}"
                                :class="fromBase === {{ $b }}
                                    ? '{{ match($b) { 2 => 'bg-violet-600 text-white border-violet-600', 8 => 'bg-amber-500 text-white border-amber-500', 10 => 'bg-emerald-600 text-white border-emerald-600', 16 => 'bg-blue-600 text-white border-blue-600' } }}'
                                    : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                                class="rounded-lg border px-4 py-2 text-sm font-mono font-bold transition-colors">
                            {{ $label }} <span class="ml-1 font-normal opacity-70">(base {{ $b }})</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Number input --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.base_converter.label_number') }}
                </label>
                <input type="text"
                       name="number"
                       x-ref="numberInput"
                       placeholder="{{ __('tools.base_converter.placeholder_number') }}"
                       autocomplete="off"
                       spellcheck="false"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 sm:max-w-sm">
            </div>
        </form>

        <div id="result" class="mt-6">
            <p class="text-sm text-slate-400">{{ __('tools.base_converter.idle') }}</p>
        </div>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('baseConverter', () => ({
        fromBase: 10,
    }))
})
</script>
@endpush
