<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('ui.app_name')) — {{ __('ui.app_name') }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Stile e librerie via CDN: nessun build step (no Node/npm/Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org@1.9.12" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak]{display:none !important;}</style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">
    <header class="bg-slate-900 text-slate-100 shadow">
        <nav class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold tracking-tight">
                <span class="text-emerald-400">⚙</span>
                <span>{{ __('ui.app_name') }}</span>
            </a>

            <div class="flex items-center gap-4 text-sm">
                {{-- Menu Strumenti --}}
                <div class="relative"
                     x-data="{ open: false, dropTop: 0 }"
                     @click.outside="open = false">
                    <button type="button"
                            x-ref="btn"
                            @click="open = !open; if (open) dropTop = $refs.btn.getBoundingClientRect().bottom + 8"
                            class="flex items-center gap-1 hover:text-emerald-400">
                        {{ __('ui.nav_tools') }}
                        <span class="text-xs" x-text="open ? '▲' : '▼'"></span>
                    </button>

                    {{-- Mega-menu:
                         mobile  → fixed, larghezza viewport, posizionato sotto il bottone via JS
                         desktop → absolute right-0, griglia 3 colonne --}}
                    <div x-show="open" x-cloak x-transition
                         :style="window.innerWidth < 1024
                                    ? `position:fixed;top:${dropTop}px;left:8px;right:8px`
                                    : ''"
                         class="lg:absolute lg:right-0 lg:mt-2 lg:w-[680px]
                                z-50 max-h-[80vh] overflow-y-auto
                                rounded-md border border-slate-200 bg-white p-4 lg:p-5 text-slate-700 shadow-xl">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-5">

                            {{-- Subnet & IP --}}
                            <div>
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('ui.group_subnet_ip') }}</p>
                                <div class="space-y-0.5">
                                    <a href="{{ route('tools.subnet-calculator.index') }}" class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.subnet_calculator.menu') }}</a>
                                    <a href="{{ route('tools.ipv6-calculator.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.ipv6_calculator.menu') }}</a>
                                    <a href="{{ route('tools.cidr-cheatsheet.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.cidr_cheatsheet.menu') }}</a>
                                    <a href="{{ route('tools.vlan-calculator.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.vlan_calculator.menu') }}</a>
                                    <a href="{{ route('tools.ip-geolocation.index') }}"   class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.ip_geolocation.menu') }}</a>
                                </div>
                            </div>

                            {{-- Diagnostica & Lookup --}}
                            <div>
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('ui.group_diagnostics') }}</p>
                                <div class="space-y-0.5">
                                    <a href="{{ route('tools.dns-lookup.index') }}"       class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.dns_lookup.menu') }}</a>
                                    <a href="{{ route('tools.ping-traceroute.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.ping_traceroute.menu') }}</a>
                                    <a href="{{ route('tools.whois.index') }}"            class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.whois.menu') }}</a>
                                    <a href="{{ route('tools.mac-lookup.index') }}"       class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.mac_lookup.menu') }}</a>
                                    <a href="{{ route('tools.ssl-checker.index') }}"      class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.ssl_checker.menu') }}</a>
                                    <a href="{{ route('tools.port-checker.index') }}"     class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.port_checker.menu') }}</a>
                                </div>
                            </div>

                            {{-- Riferimenti --}}
                            <div>
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('ui.group_references') }}</p>
                                <div class="space-y-0.5">
                                    <a href="{{ route('tools.port-reference.index') }}"   class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.port_reference.menu') }}</a>
                                    <a href="{{ route('tools.osi-model.index') }}"        class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.osi_model.menu') }}</a>
                                    <a href="{{ route('tools.linux-cheatsheet.index') }}" class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.linux_cheatsheet.menu') }}</a>
                                    <a href="{{ route('tools.http-status-codes.index') }}" class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.http_status_codes.menu') }}</a>
                                    <a href="{{ route('tools.rfc-browser.index') }}"      class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.rfc_browser.menu') }}</a>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div>
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('ui.group_email') }}</p>
                                <div class="space-y-0.5">
                                    <a href="{{ route('tools.email-header-analyzer.index') }}" class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.email_header_analyzer.menu') }}</a>
                                    <a href="{{ route('tools.email-deliverability.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.email_deliverability.menu') }}</a>
                                    <a href="{{ route('tools.blacklist-checker.index') }}"     class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.blacklist_checker.menu') }}</a>
                                    <a href="{{ route('tools.mx-checker.index') }}"            class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.mx_checker.menu') }}</a>
                                    <a href="{{ route('tools.email-validator.index') }}"       class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.email_validator.menu') }}</a>
                                </div>
                            </div>

                            {{-- Strumenti --}}
                            <div>
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('ui.group_tools') }}</p>
                                <div class="space-y-0.5">
                                    <a href="{{ route('tools.regex-tester.index') }}"         class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.regex_tester.menu') }}</a>
                                    <a href="{{ route('tools.base-converter.index') }}"        class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.base_converter.menu') }}</a>
                                    <a href="{{ route('tools.bandwidth-calculator.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.bandwidth_calculator.menu') }}</a>
                                    <a href="{{ route('tools.formatter.index') }}"             class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.formatter.menu') }}</a>
                                    <a href="{{ route('tools.markdown-viewer.index') }}"       class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.markdown_viewer.menu') }}</a>
                                </div>
                            </div>

                            {{-- Cablaggio --}}
                            <div>
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('ui.group_cabling') }}</p>
                                <div class="space-y-0.5">
                                    <a href="{{ route('tools.cable-schemas.index') }}" class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.cable_schemas.menu') }}</a>
                                    <a href="{{ route('tools.cable-colors.index') }}"  class="block rounded px-2 py-1 text-sm hover:bg-slate-100">{{ __('tools.cable_colors.menu') }}</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Switcher lingua: POST lato server, salva in sessione --}}
                <div class="flex items-center gap-1" aria-label="{{ __('ui.language') }}">
                    @foreach (['it', 'en'] as $loc)
                        <form method="POST" action="{{ route('language.switch', $loc) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded px-2 py-1 text-xs font-medium uppercase
                                           {{ app()->getLocale() === $loc
                                                ? 'bg-emerald-500 text-slate-900'
                                                : 'bg-slate-700 text-slate-200 hover:bg-slate-600' }}">
                                {{ $loc }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </nav>
    </header>

    {{-- Banner IP pubblico: fetch client-side da api.ipify.org; fallback all'IP visto dal server --}}
    <div class="border-b border-slate-600 bg-slate-700 py-3 text-center"
         x-data="{ ip: '' }"
         x-init="fetch('https://api.ipify.org?format=json').then(r=>r.json()).then(d=>{ip=d.ip}).catch(()=>{ip='{{ e(request()->ip()) }}'})">
        <span class="text-base font-medium text-slate-200">{{ __('ui.your_ip') }}</span>
        <span x-show="!ip" class="ml-2 font-mono text-sm italic text-slate-400">{{ __('ui.ip_loading') }}</span>
        <button type="button"
                x-show="ip"
                x-text="ip"
                @click="$dispatch('fill-ip', ip)"
                title="{{ __('ui.ip_click_hint') }}"
                class="ml-2 cursor-pointer font-mono text-xl font-bold text-emerald-300 hover:text-emerald-200 hover:underline">
        </button>
    </div>

    <main @class([
        'w-full px-4 py-4'        => $__env->hasSection('wide_content'),
        'mx-auto max-w-5xl px-4 py-8' => ! $__env->hasSection('wide_content'),
    ])>
        @hasSection('wide_content')
            @yield('wide_content')
        @else
            @yield('content')
        @endif
    </main>

    <footer class="mx-auto max-w-5xl px-4 py-8 text-center text-xs text-slate-400">
        <a href="https://github.com/lejubila/SAT4all"
           target="_blank" rel="noopener noreferrer"
           class="hover:text-slate-200 hover:underline transition-colors">
            github.com/lejubila/SAT4all
        </a>
    </footer>
    @stack('scripts')
</body>
</html>
