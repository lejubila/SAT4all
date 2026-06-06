@extends('layouts.app')

@section('title', __('tools.rfc_browser.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.rfc_browser.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.rfc_browser.description') }}
        </p>
    </section>

    <div x-data="rfcBrowser">

        {{-- Search + filters ------------------------------------------------}}
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="search"
                   x-model="query"
                   placeholder="{{ __('tools.rfc_browser.search_placeholder') }}"
                   class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">

            {{-- Category filter --}}
            <div class="flex flex-wrap gap-1">
                <button type="button"
                        @click="activeCategory = ''"
                        :class="activeCategory === '' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
                        class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium transition-colors">
                    {{ __('tools.rfc_browser.filter_all') }}
                </button>
                @foreach ($categories as $cat)
                    <button type="button"
                            @click="activeCategory = '{{ $cat }}'"
                            :class="activeCategory === '{{ $cat }}' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
                            class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium capitalize transition-colors">
                        {{ __('tools.rfc_browser.cat_' . $cat) }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Status filter pills ---------------------------------------------}}
        <div class="mb-4 flex flex-wrap gap-1">
            <button type="button"
                    @click="activeStatus = ''"
                    :class="activeStatus === '' ? 'ring-2 ring-slate-400' : 'opacity-70 hover:opacity-100'"
                    class="rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 transition-all">
                {{ __('tools.rfc_browser.filter_all') }} {{ __('tools.rfc_browser.filter_status') }}
            </button>
            @foreach ([
                'std'  => ['bg-emerald-100 text-emerald-800 border-emerald-300', 'status_std'],
                'ps'   => ['bg-blue-100 text-blue-800 border-blue-300',           'status_ps'],
                'bcp'  => ['bg-purple-100 text-purple-800 border-purple-300',     'status_bcp'],
                'info' => ['bg-slate-100 text-slate-700 border-slate-300',        'status_info'],
                'hist' => ['bg-orange-100 text-orange-800 border-orange-300',     'status_hist'],
            ] as $s => [$classes, $key])
                <button type="button"
                        @click="activeStatus = '{{ $s }}'"
                        :class="activeStatus === '{{ $s }}' ? 'ring-2 ring-offset-1 ring-slate-500' : 'opacity-70 hover:opacity-100'"
                        class="rounded border {{ $classes }} px-2 py-0.5 text-xs font-medium transition-all">
                    {{ __('tools.rfc_browser.' . $key) }}
                </button>
            @endforeach
        </div>

        {{-- Count + table ---------------------------------------------------}}
        <p class="mb-2 text-xs text-slate-500">
            <span x-text="filtered.length"></span> {{ __('tools.rfc_browser.count_unit') }}
        </p>

        <div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-3 py-2 text-left">{{ __('tools.rfc_browser.col_number') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('tools.rfc_browser.col_title') }}</th>
                        <th class="hidden px-3 py-2 text-center sm:table-cell">{{ __('tools.rfc_browser.col_year') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('tools.rfc_browser.col_status') }}</th>
                        <th class="hidden px-3 py-2 text-left lg:table-cell">{{ __('tools.rfc_browser.col_category') }}</th>
                        <th class="px-3 py-2 text-center">{{ __('tools.rfc_browser.col_link') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                {{ __('tools.rfc_browser.no_results') }}
                            </td>
                        </tr>
                    </template>
                    <template x-for="rfc in filtered" :key="rfc.number">
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2.5">
                                <span class="font-mono font-semibold text-slate-800" x-text="'RFC ' + rfc.number"></span>
                            </td>
                            <td class="px-3 py-2.5 text-slate-700" x-text="rfc.title"></td>
                            <td class="hidden px-3 py-2.5 text-center text-slate-500 sm:table-cell" x-text="rfc.year"></td>
                            <td class="px-3 py-2.5">
                                <span class="rounded px-1.5 py-0.5 text-xs font-medium"
                                      :class="statusClass(rfc.status)"
                                      x-text="statusLabel(rfc.status)">
                                </span>
                            </td>
                            <td class="hidden px-3 py-2.5 lg:table-cell">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600"
                                      x-text="categoryLabel(rfc.category)">
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <a :href="'https://www.rfc-editor.org/rfc/rfc' + rfc.number"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 rounded bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100">
                                    {{ __('tools.rfc_browser.open_rfc') }}
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Note ------------------------------------------------------------}}
        <p class="mt-3 text-xs text-slate-400">{{ __('tools.rfc_browser.note_source') }}</p>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('rfcBrowser', () => ({
        query: '',
        activeCategory: '',
        activeStatus: '',
        rfcs: @json($rfcs),

        statusLabels: {
            std:  '{{ __('tools.rfc_browser.status_std') }}',
            ps:   '{{ __('tools.rfc_browser.status_ps') }}',
            bcp:  '{{ __('tools.rfc_browser.status_bcp') }}',
            info: '{{ __('tools.rfc_browser.status_info') }}',
            hist: '{{ __('tools.rfc_browser.status_hist') }}',
        },

        categoryLabels: {
            networking: '{{ __('tools.rfc_browser.cat_networking') }}',
            routing:    '{{ __('tools.rfc_browser.cat_routing') }}',
            dns:        '{{ __('tools.rfc_browser.cat_dns') }}',
            email:      '{{ __('tools.rfc_browser.cat_email') }}',
            web:        '{{ __('tools.rfc_browser.cat_web') }}',
            security:   '{{ __('tools.rfc_browser.cat_security') }}',
            management: '{{ __('tools.rfc_browser.cat_management') }}',
            reference:  '{{ __('tools.rfc_browser.cat_reference') }}',
        },

        get filtered() {
            const q   = this.query.toLowerCase().trim()
            const cat = this.activeCategory
            const st  = this.activeStatus

            return this.rfcs.filter(r => {
                const matchCat = !cat || r.category === cat
                const matchSt  = !st  || r.status   === st
                const matchQ   = !q
                    || String(r.number).includes(q)
                    || r.title.toLowerCase().includes(q)
                    || r.category.toLowerCase().includes(q)

                return matchCat && matchSt && matchQ
            })
        },

        statusClass(status) {
            const map = {
                std:  'bg-emerald-100 text-emerald-800',
                ps:   'bg-blue-100 text-blue-800',
                bcp:  'bg-purple-100 text-purple-800',
                info: 'bg-slate-100 text-slate-700',
                hist: 'bg-orange-100 text-orange-800',
            }
            return map[status] || 'bg-slate-100 text-slate-600'
        },

        statusLabel(status) {
            return this.statusLabels[status] || status
        },

        categoryLabel(cat) {
            return this.categoryLabels[cat] || cat
        },
    }))
})
</script>
@endpush
