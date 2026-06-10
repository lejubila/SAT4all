@extends('layouts.app')

@section('title', __('tools.markdown_viewer.title'))

@push('styles')
<style>
    /* Ensure Tailwind typography plugin classes work with CDN */
    .prose pre { background-color: #1e293b; border-radius: 0.375rem; padding: 1rem; overflow-x: auto; }
    .prose pre code { background: transparent; padding: 0; color: #e2e8f0; font-size: 0.875em; }
    .prose code { background-color: #f1f5f9; border-radius: 0.25rem; padding: 0.125rem 0.375rem; font-size: 0.875em; }
    .prose table { width: 100%; border-collapse: collapse; }
    .prose th, .prose td { border: 1px solid #e2e8f0; padding: 0.5rem 0.75rem; }
    .prose th { background-color: #f8fafc; font-weight: 600; }
    .prose blockquote { border-left: 4px solid #10b981; padding-left: 1rem; color: #64748b; font-style: italic; }
    .prose hr { border-color: #e2e8f0; }
    .prose ul { list-style-type: disc; padding-left: 1.5rem; }
    .prose ol { list-style-type: decimal; padding-left: 1.5rem; }
    .prose li { margin-bottom: 0.25rem; }
    .prose h1 { font-size: 1.75rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.3em; }
    .prose h2 { font-size: 1.4rem; font-weight: 600; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.3em; }
    .prose h3 { font-size: 1.15rem; font-weight: 600; }
    .prose a { color: #059669; text-decoration: underline; }
    .prose img { max-width: 100%; border-radius: 0.375rem; }
    /* Checkbox lists (GFM task lists) */
    .prose input[type="checkbox"] { margin-right: 0.375rem; }
</style>
@endpush

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.markdown_viewer.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.markdown_viewer.description') }}
        </p>
    </section>

    <div x-data="{ md: '' }">

        {{-- Layout a due colonne --}}
        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Colonna sinistra: editor --}}
            <div class="flex flex-col gap-3">
                <label class="text-sm font-medium text-slate-700">
                    {{ __('tools.markdown_viewer.label_input') }}
                </label>
                <textarea
                    name="markdown"
                    x-model="md"
                    hx-post="{{ route('tools.markdown-viewer.preview') }}"
                    hx-target="#md-preview"
                    hx-swap="innerHTML"
                    hx-trigger="input changed delay:600ms"
                    hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
                    placeholder="{{ __('tools.markdown_viewer.placeholder_input') }}"
                    spellcheck="false"
                    class="h-96 w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2 font-mono text-sm leading-relaxed text-slate-800 shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                ></textarea>

                {{-- Pulsanti export --}}
                <div class="flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('tools.markdown-viewer.export-html') }}">
                        @csrf
                        <input type="hidden" name="markdown" :value="md">
                        <button type="submit"
                                :disabled="!md.trim()"
                                class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('tools.markdown_viewer.btn_export_html') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('tools.markdown-viewer.export-pdf') }}">
                        @csrf
                        <input type="hidden" name="markdown" :value="md">
                        <button type="submit"
                                :disabled="!md.trim()"
                                class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('tools.markdown_viewer.btn_export_pdf') }}
                        </button>
                    </form>
                </div>

                <p class="text-xs text-slate-400">
                    {{ __('tools.markdown_viewer.hint_limit') }}
                </p>
            </div>

            {{-- Colonna destra: preview --}}
            <div>
                <p class="mb-2 text-sm font-medium text-slate-700">
                    {{ __('tools.markdown_viewer.label_preview') }}
                </p>
                <div id="md-preview"
                     class="min-h-96 rounded-lg border border-slate-200 bg-white p-5 shadow-sm overflow-auto">
                    <p class="text-sm text-slate-400 italic">{{ __('tools.markdown_viewer.placeholder_preview') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
