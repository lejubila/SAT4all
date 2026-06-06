@extends('layouts.app')

@section('title', __('tools.regex_tester.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.regex_tester.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.regex_tester.description') }}
        </p>
    </section>

    <div x-data="regexTester" class="space-y-4">

        <form hx-post="{{ route('tools.regex-tester.test') }}"
              hx-target="#result"
              hx-swap="innerHTML"
              hx-trigger="submit"
              class="space-y-4">
            @csrf

            {{-- Pattern --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.regex_tester.label_pattern') }}
                </label>
                <div class="flex items-center gap-0">
                    <span class="inline-flex h-10 items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 px-3 font-mono text-sm text-slate-500">~</span>
                    <input type="text"
                           name="pattern"
                           x-model="pattern"
                           placeholder="{{ __('tools.regex_tester.placeholder_pattern') }}"
                           autocomplete="off"
                           spellcheck="false"
                           class="h-10 flex-1 border border-slate-300 px-3 font-mono text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <span class="inline-flex h-10 items-center rounded-r-lg border border-l-0 border-slate-300 bg-slate-100 px-2 font-mono text-sm text-slate-500"
                          x-text="'~' + flagString"></span>
                </div>
            </div>

            {{-- Flags --}}
            <div>
                <p class="mb-1 text-sm font-medium text-slate-700">{{ __('tools.regex_tester.label_flags') }}</p>
                <div class="flex flex-wrap gap-3">
                    @foreach (['i', 'm', 's', 'u'] as $f)
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox"
                                   x-model="flags.{{ $f }}"
                                   class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400">
                            <span class="font-mono font-semibold text-emerald-700">{{ $f }}</span>
                            <span class="text-slate-500">{{ __('tools.regex_tester.flag_' . $f) }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="hidden" name="flags" :value="flagString">
            </div>

            {{-- Subject --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.regex_tester.label_subject') }}
                </label>
                <textarea name="subject"
                          rows="6"
                          placeholder="{{ __('tools.regex_tester.placeholder_subject') }}"
                          spellcheck="false"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200"></textarea>
            </div>

            {{-- Replacement (optional) --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.regex_tester.label_replacement') }}
                </label>
                <input type="text"
                       name="replacement"
                       placeholder="{{ __('tools.regex_tester.placeholder_replacement') }}"
                       spellcheck="false"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                {{ __('tools.regex_tester.btn_test') }}
            </button>
        </form>

        <div id="result" class="mt-2">
            <p class="text-sm text-slate-400">{{ __('tools.regex_tester.idle') }}</p>
        </div>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('regexTester', () => ({
        pattern: '',
        flags: { i: false, m: false, s: false, u: false },

        get flagString() {
            return Object.entries(this.flags)
                .filter(([, v]) => v)
                .map(([k]) => k)
                .join('')
        },
    }))
})
</script>
@endpush
