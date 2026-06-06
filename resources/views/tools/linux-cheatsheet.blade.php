@extends('layouts.app')

@section('title', __('tools.linux_cheatsheet.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.linux_cheatsheet.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.linux_cheatsheet.description') }}
        </p>
    </section>

    <div x-data="cheatsheet">

        {{-- Barra di ricerca --}}
        <div class="mb-6">
            <input type="search"
                   x-model="query"
                   placeholder="{{ __('tools.linux_cheatsheet.search_placeholder') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm shadow-sm
                          focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>

        {{-- Nessun risultato --}}
        <p x-show="query.trim() && filtered.length === 0"
           x-cloak
           class="text-sm text-slate-500">
            {{ __('tools.linux_cheatsheet.no_results') }}
        </p>

        {{-- Categorie --}}
        <template x-for="cat in filtered" :key="cat.key">
            <section class="mb-8">
                <h2 class="mb-3 text-base font-semibold text-slate-700" x-text="cat.label"></h2>

                <div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="w-64 px-4 py-2 text-left">{{ __('tools.linux_cheatsheet.col_command') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('tools.linux_cheatsheet.col_description') }}</th>
                                <th class="hidden px-4 py-2 text-left lg:table-cell">{{ __('tools.linux_cheatsheet.col_example') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="entry in cat.commands" :key="entry.cmd">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5 align-top">
                                        <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-xs text-slate-800"
                                              x-text="entry.cmd"></code>
                                    </td>
                                    <td class="px-4 py-2.5 align-top text-slate-600" x-text="entry.desc"></td>
                                    <td class="hidden px-4 py-2.5 align-top lg:table-cell">
                                        <code class="font-mono text-xs text-emerald-700" x-text="entry.example"></code>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </template>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cheatsheet', () => ({
            query: '',
            categories: @json($categories),
            get filtered() {
                const q = this.query.toLowerCase().trim()
                if (!q) return this.categories
                return this.categories
                    .map(cat => ({
                        key: cat.key,
                        label: cat.label,
                        commands: cat.commands.filter(c =>
                            c.cmd.toLowerCase().includes(q) ||
                            c.desc.toLowerCase().includes(q) ||
                            c.example.toLowerCase().includes(q)
                        )
                    }))
                    .filter(cat => cat.commands.length > 0)
            }
        }))
    })
</script>
@endpush
