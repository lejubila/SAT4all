@extends('layouts.app')

@section('title', __('tools.http_status_codes.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.http_status_codes.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.http_status_codes.description') }}
        </p>
    </section>

    <div x-data="httpStatusCodes">

        {{-- Search + category filters --}}
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search"
                   x-model="query"
                   placeholder="{{ __('tools.http_status_codes.search_placeholder') }}"
                   class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm shadow-sm
                          focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">

            <div class="flex flex-wrap gap-1">
                <button type="button"
                        @click="activeCategory = ''"
                        :class="activeCategory === '' ? 'bg-slate-700 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
                        class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium transition-colors">
                    {{ __('tools.http_status_codes.filter_all') }}
                </button>
                @foreach ([
                    '1xx' => 'bg-slate-500',
                    '2xx' => 'bg-emerald-600',
                    '3xx' => 'bg-blue-600',
                    '4xx' => 'bg-amber-500',
                    '5xx' => 'bg-red-600',
                ] as $cat => $activeBg)
                    <button type="button"
                            @click="activeCategory = '{{ $cat }}'"
                            :class="activeCategory === '{{ $cat }}' ? '{{ $activeBg }} text-white border-transparent' : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200'"
                            class="rounded-full border px-3 py-1 text-xs font-medium transition-colors">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Count --}}
        <p class="mb-2 text-xs text-slate-500">
            <span x-text="filtered.length"></span> {{ __('tools.http_status_codes.count_unit') }}
        </p>

        {{-- Table --}}
        <div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-2 text-center w-20">{{ __('tools.http_status_codes.col_code') }}</th>
                        <th class="px-3 py-2 text-left w-56">{{ __('tools.http_status_codes.col_name') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('tools.http_status_codes.col_desc') }}</th>
                        <th class="hidden px-3 py-2 text-center w-24 sm:table-cell">{{ __('tools.http_status_codes.col_rfc') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                {{ __('tools.http_status_codes.no_results') }}
                            </td>
                        </tr>
                    </template>
                    <template x-for="c in filtered" :key="c.code">
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 text-center">
                                <span class="inline-block min-w-[3rem] rounded px-2 py-0.5 font-mono text-sm font-bold"
                                      :class="badgeClass(c.category)"
                                      x-text="c.code">
                                </span>
                            </td>
                            <td class="px-3 py-3 font-medium text-slate-800" x-text="c.name"></td>
                            <td class="px-3 py-3 text-slate-600" x-text="c.desc"></td>
                            <td class="hidden px-3 py-3 text-center sm:table-cell">
                                <span class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-xs text-slate-500"
                                      x-text="c.rfc">
                                </span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('httpStatusCodes', () => ({
        query: '',
        activeCategory: '',
        codes: @json($codes),

        get filtered() {
            const q   = this.query.toLowerCase().trim()
            const cat = this.activeCategory

            return this.codes.filter(c => {
                const matchCat = !cat || c.category === cat
                const matchQ   = !q
                    || String(c.code).includes(q)
                    || c.name.toLowerCase().includes(q)
                    || c.desc.toLowerCase().includes(q)
                return matchCat && matchQ
            })
        },

        badgeClass(category) {
            const map = {
                '1xx': 'bg-slate-200 text-slate-700',
                '2xx': 'bg-emerald-100 text-emerald-800',
                '3xx': 'bg-blue-100 text-blue-800',
                '4xx': 'bg-amber-100 text-amber-800',
                '5xx': 'bg-red-100 text-red-800',
            }
            return map[category] || 'bg-slate-100 text-slate-600'
        },
    }))
})
</script>
@endpush
