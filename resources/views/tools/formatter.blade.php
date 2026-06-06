@extends('layouts.app')

@section('title', __('tools.formatter.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.formatter.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.formatter.description') }}
        </p>
    </section>

    <div x-data="formatter">

        <form hx-post="{{ route('tools.formatter.format') }}"
              hx-target="#result"
              hx-swap="innerHTML"
              hx-trigger="submit"
              class="space-y-4">
            @csrf
            <input type="hidden" name="format" :value="format">
            <input type="hidden" name="indent" :value="indent">

            {{-- Format selector --}}
            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">{{ __('tools.formatter.label_format') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (['auto' => 'format_auto', 'json' => 'format_json', 'xml' => 'format_xml', 'html' => 'format_html'] as $f => $key)
                        <button type="button"
                                @click="format = '{{ $f }}'"
                                :class="format === '{{ $f }}'
                                    ? 'bg-emerald-600 text-white border-emerald-600'
                                    : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                                class="rounded-lg border px-4 py-1.5 text-sm font-medium transition-colors">
                            {{ __('tools.formatter.' . $key) }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Indent selector --}}
            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">{{ __('tools.formatter.label_indent') }}</p>
                <div class="flex gap-2">
                    @foreach ([2 => 'indent_2', 4 => 'indent_4'] as $n => $key)
                        <button type="button"
                                @click="indent = {{ $n }}"
                                :class="indent === {{ $n }}
                                    ? 'bg-slate-700 text-white border-slate-700'
                                    : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                                class="rounded-lg border px-3 py-1.5 text-sm font-medium transition-colors">
                            {{ __('tools.formatter.' . $key) }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Input textarea --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.formatter.label_input') }}
                </label>
                <textarea name="input"
                          rows="12"
                          placeholder="{{ __('tools.formatter.placeholder_input') }}"
                          spellcheck="false"
                          autocomplete="off"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200"></textarea>
            </div>

            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                {{ __('tools.formatter.btn_format') }}
            </button>
        </form>

        <div id="result" class="mt-6">
            <p class="text-sm text-slate-400">{{ __('tools.formatter.idle') }}</p>
        </div>

    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1/themes/prism-okaidia.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/prismjs@1/prism.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1/components/prism-json.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('formatter', () => ({
        format: 'auto',
        indent: 4,
    }))
})

document.body.addEventListener('htmx:afterSwap', () => {
    if (window.Prism) Prism.highlightAll()
})
</script>
@endpush
