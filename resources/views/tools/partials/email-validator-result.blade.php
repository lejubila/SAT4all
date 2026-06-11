{{-- OOB: aggiorna il codice captcha --}}
<div id="email-validator-captcha"
     hx-swap-oob="true"
     class="inline-block select-none rounded-lg border border-slate-300 bg-slate-100 px-6 py-3
            font-mono text-2xl font-bold tracking-[0.4em] text-emerald-700 shadow-inner">
    {{ $newCaptcha ?? '' }}
</div>

{{-- Validation errors --}}
@if (! empty($validationErrors ?? null))
    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
        @foreach ($validationErrors as $msg)
            <p class="text-sm font-medium text-red-700">{{ $msg }}</p>
        @endforeach
    </div>

@elseif ($result !== null)

    @php
        $overall = $result['overall'];
        $overallStyles = match ($overall) {
            'valid'   => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-300', 'text' => 'text-emerald-800', 'badge' => 'bg-emerald-100 text-emerald-800'],
            'invalid' => ['bg' => 'bg-red-50',     'border' => 'border-red-300',     'text' => 'text-red-800',     'badge' => 'bg-red-100 text-red-700'],
            'risky'   => ['bg' => 'bg-amber-50',   'border' => 'border-amber-300',   'text' => 'text-amber-800',   'badge' => 'bg-amber-100 text-amber-800'],
            default   => ['bg' => 'bg-slate-50',   'border' => 'border-slate-300',   'text' => 'text-slate-700',   'badge' => 'bg-slate-100 text-slate-700'],
        };
        $overallLabel = __('tools.email_validator.overall_' . $overall);
    @endphp

    {{-- Overall result banner --}}
    <div class="mb-5 rounded-xl border {{ $overallStyles['border'] }} {{ $overallStyles['bg'] }} px-5 py-4">
        <p class="text-xs font-semibold uppercase tracking-wide {{ $overallStyles['text'] }} opacity-70 mb-1">
            {{ __('tools.email_validator.section_result') }}
        </p>
        <div class="flex items-center gap-3">
            <span class="rounded-full px-3 py-1 text-sm font-bold {{ $overallStyles['badge'] }}">
                {{ $overallLabel }}
            </span>
            <span class="font-mono text-sm font-semibold {{ $overallStyles['text'] }}">{{ $result['email'] }}</span>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Syntax --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    {{ __('tools.email_validator.section_syntax') }}
                </span>
            </div>
            <div class="px-4 py-3 flex flex-wrap gap-4">
                <div>
                    <span class="text-xs text-slate-500">{{ __('tools.email_validator.local_part') }}</span>
                    <span class="ml-2 font-mono text-sm text-slate-800">{{ $result['syntax']['local'] ?: '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500">{{ __('tools.email_validator.domain_part') }}</span>
                    <span class="ml-2 font-mono text-sm text-slate-800">{{ $result['syntax']['domain'] ?: '—' }}</span>
                </div>
                <div class="ml-auto">
                    @if ($result['syntax']['valid'])
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                            {{ __('tools.email_validator.syntax_valid') }}
                        </span>
                    @else
                        <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                            {{ __('tools.email_validator.syntax_invalid') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- MX --}}
        @if ($result['syntax']['valid'])
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    {{ __('tools.email_validator.section_mx') }}
                </span>
            </div>
            <div class="px-4 py-3">
                @if (! $result['mx']['found'])
                    <p class="text-sm text-red-700">{{ __('tools.email_validator.mx_not_found') }}</p>
                @elseif ($result['mx']['fallback'])
                    <p class="text-sm text-amber-700">{{ __('tools.email_validator.mx_fallback') }}</p>
                @else
                    <p class="mb-2 text-sm text-slate-600">
                        {{ __('tools.email_validator.mx_found', ['count' => count($result['mx']['records'])]) }}
                    </p>
                    <div class="space-y-1">
                        @foreach ($result['mx']['records'] as $rec)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                                    {{ __('tools.email_validator.mx_priority') }} {{ $rec['pri'] }}
                                </span>
                                <span class="font-mono text-slate-800">{{ $rec['host'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- SMTP --}}
        @if ($result['mx']['found'])
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    {{ __('tools.email_validator.section_smtp') }}
                </span>
            </div>
            <div class="px-4 py-3 space-y-2">
                @php $smtpResult = $result['smtp']['result']; @endphp

                @if (! $result['smtp']['checked'])
                    <p class="text-sm text-slate-500">
                        {{ __('tools.email_validator.smtp_' . ($smtpResult === 'skipped' ? 'skipped' : 'unavailable')) }}
                    </p>
                @else
                    <div class="flex flex-wrap items-center gap-3">
                        @php
                            $smtpBadge = match ($smtpResult) {
                                'valid'    => 'bg-emerald-100 text-emerald-800',
                                'invalid'  => 'bg-red-100 text-red-700',
                                'catchall' => 'bg-amber-100 text-amber-800',
                                'risky'    => 'bg-amber-100 text-amber-800',
                                default    => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $smtpBadge }}">
                            {{ __('tools.email_validator.smtp_' . $smtpResult) }}
                        </span>
                        @if ($result['smtp']['code'])
                            <span class="text-xs text-slate-500">
                                {{ __('tools.email_validator.smtp_code') }}:
                                <span class="font-mono font-semibold text-slate-700">{{ $result['smtp']['code'] }}</span>
                            </span>
                        @endif
                    </div>
                    @if ($result['smtp']['message'])
                        <p class="font-mono text-xs text-slate-500 break-all">{{ $result['smtp']['message'] }}</p>
                    @endif
                @endif
            </div>
        </div>
        @endif

    </div>

@endif
