@php $error = $error ?? null; @endphp

@if ($error)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $error }}
    </div>
@elseif (blank($html ?? null))
    <p class="text-sm text-slate-400 italic">{{ __('tools.markdown_viewer.placeholder_preview') }}</p>
@else
    <div class="prose prose-slate max-w-none
                prose-headings:text-slate-900
                prose-a:text-emerald-700
                prose-code:bg-slate-100 prose-code:rounded prose-code:px-1
                prose-pre:bg-slate-800 prose-pre:text-slate-100
                prose-blockquote:border-emerald-400 prose-blockquote:text-slate-600
                prose-table:text-sm
                prose-th:bg-slate-100 prose-th:font-semibold
                prose-img:rounded-lg">
        {!! $html !!}
    </div>
@endif
