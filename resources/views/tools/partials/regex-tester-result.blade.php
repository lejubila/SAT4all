@php
    $idle    = $result['idle'] ?? false;
    $valid   = $result['valid'] ?? false;
    $error   = $result['error'] ?? null;
    $count   = $result['match_count'] ?? 0;
    $matches = $result['matches'] ?? [];
    $hl      = $result['highlighted'] ?? '';
    $trunc   = $result['truncated'] ?? false;
    $hasRepl = array_key_exists('replacement', $result);
    $repl    = $result['replacement'] ?? null;
@endphp

@if ($idle)
    <p class="text-sm text-slate-400">{{ __('tools.regex_tester.idle') }}</p>

@elseif (! $valid)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <span class="font-semibold">{{ __('tools.regex_tester.error_invalid') }}</span>
        {{ $error }}
    </div>

@else
    {{-- Match count badge --}}
    <div class="mb-4 flex items-center gap-3">
        @if ($count === 0)
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">
                {{ __('tools.regex_tester.match_count_zero') }}
            </span>
        @elseif ($count === 1)
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">
                {{ __('tools.regex_tester.match_count_one') }}
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">
                {{ str_replace(':count', $count, __('tools.regex_tester.match_count_many')) }}
            </span>
        @endif
        @if ($trunc)
            <span class="text-xs text-amber-600">
                {{ str_replace(':n', count($matches), __('tools.regex_tester.truncated_notice')) }}
            </span>
        @endif
    </div>

    {{-- Highlighted subject --}}
    @if ($hl !== '')
        <div class="mb-4">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                {{ __('tools.regex_tester.section_highlight') }}
            </p>
            <div class="overflow-x-auto rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 font-mono text-sm leading-relaxed text-slate-800 whitespace-pre-wrap">{!! $hl !!}</div>
        </div>
    @endif

    {{-- Replacement result --}}
    @if ($hasRepl)
        <div class="mb-4">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                {{ __('tools.regex_tester.section_replace') }}
            </p>
            @if ($repl === false)
                <p class="text-sm text-red-600">{{ __('tools.regex_tester.error_invalid') }}</p>
            @else
                <div class="overflow-x-auto rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 font-mono text-sm leading-relaxed text-slate-800 whitespace-pre-wrap">{{ $repl }}</div>
            @endif
        </div>
    @endif

    {{-- Matches table --}}
    @if (count($matches) > 0)
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                {{ __('tools.regex_tester.section_matches') }}
            </p>
            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-right w-10">{{ __('tools.regex_tester.col_n') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('tools.regex_tester.col_match') }}</th>
                            <th class="px-3 py-2 text-right w-20">{{ __('tools.regex_tester.col_start') }}</th>
                            <th class="px-3 py-2 text-right w-20">{{ __('tools.regex_tester.col_end') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('tools.regex_tester.col_groups') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($matches as $i => $m)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 text-right font-mono text-xs text-slate-400">{{ $i + 1 }}</td>
                                <td class="px-3 py-2 font-mono">
                                    <span class="rounded bg-amber-100 px-1.5 py-0.5 text-amber-900">{{ $m['value'] }}</span>
                                </td>
                                <td class="px-3 py-2 text-right font-mono text-xs text-slate-500">{{ $m['start'] }}</td>
                                <td class="px-3 py-2 text-right font-mono text-xs text-slate-500">{{ $m['end'] }}</td>
                                <td class="px-3 py-2">
                                    @if (empty($m['groups']))
                                        <span class="text-slate-300">{{ __('tools.regex_tester.no_groups') }}</span>
                                    @else
                                        <span class="flex flex-wrap gap-1">
                                            @foreach ($m['groups'] as $g)
                                                @if ($g['found'])
                                                    <span class="rounded bg-blue-100 px-1.5 py-0.5 text-xs text-blue-800"
                                                          title="{{ str_replace(':n', $g['index'], __('tools.regex_tester.group_label')) }}">
                                                        ${{ $g['index'] }}: {{ $g['value'] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif
