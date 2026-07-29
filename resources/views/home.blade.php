@extends('layouts.app')

@section('title', __('ui.home_title'))

@section('content')
    <section class="mb-10">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('ui.app_name') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('ui.tagline') }}</p>
        <p class="mt-4 max-w-2xl text-slate-600">{{ __('ui.home_intro') }}</p>
    </section>

    {{-- Preferiti: calcolati client-side da localStorage, visibili dopo la prima visita a un tool --}}
    <div x-data="{
             favs: [],
             init() {
                 const u = JSON.parse(localStorage.getItem('sat_usage') || '{}');
                 this.favs = Object.entries(u)
                     .sort(([,a],[,b]) => b - a)
                     .slice(0, 6)
                     .map(([k]) => (window.SAT_TOOLS || []).find(t => t.key === k))
                     .filter(Boolean);
             }
         }"
         x-cloak>
        <template x-if="favs.length > 0">
            <section class="mb-10">
                <div class="mb-4 flex items-baseline gap-3">
                    <h2 class="text-lg font-semibold text-slate-800">{{ __('ui.favorites_title') }}</h2>
                    <span class="text-sm text-slate-400">{{ __('ui.favorites_hint') }}</span>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <template x-for="tool in favs" :key="tool.key">
                        <a :href="tool.route"
                           class="flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50
                                  p-3 shadow-sm hover:border-amber-400 hover:bg-amber-100 transition-colors">
                            <span class="text-amber-400 text-lg leading-none">★</span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800" x-text="tool.name"></p>
                                <p class="truncate text-xs text-slate-500" x-text="tool.cat"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </section>
        </template>
    </div>

    @php
        $phases = [
            __('ui.group_subnet_ip') => [
                ['name' => __('tools.subnet_calculator.title'),    'route' => 'tools.subnet-calculator.index'],
                ['name' => __('tools.ipv6_calculator.title'),      'route' => 'tools.ipv6-calculator.index'],
                ['name' => __('tools.cidr_cheatsheet.title'),      'route' => 'tools.cidr-cheatsheet.index'],
                ['name' => __('tools.vlan_calculator.title'),      'route' => 'tools.vlan-calculator.index'],
                ['name' => __('tools.bandwidth_calculator.title'), 'route' => 'tools.bandwidth-calculator.index'],
            ],
            __('ui.group_diagnostics') => [
                ['name' => __('tools.ping_traceroute.title'),  'route' => 'tools.ping-traceroute.index'],
                ['name' => __('tools.dns_lookup.title'),        'route' => 'tools.dns-lookup.index'],
                ['name' => __('tools.whois.title'),             'route' => 'tools.whois.index'],
                ['name' => __('tools.ip_geolocation.title'),   'route' => 'tools.ip-geolocation.index'],
                ['name' => __('tools.ssl_checker.title'),       'route' => 'tools.ssl-checker.index'],
                ['name' => __('tools.port_checker.title'),     'route' => 'tools.port-checker.index'],
                ['name' => __('tools.mac_lookup.title'),        'route' => 'tools.mac-lookup.index'],
            ],
            __('ui.group_email') => [
                ['name' => __('tools.email_header_analyzer.title'), 'route' => 'tools.email-header-analyzer.index'],
                ['name' => __('tools.email_deliverability.title'),  'route' => 'tools.email-deliverability.index'],
                ['name' => __('tools.blacklist_checker.title'),     'route' => 'tools.blacklist-checker.index'],
                ['name' => __('tools.mx_checker.title'),            'route' => 'tools.mx-checker.index'],
                ['name' => __('tools.email_validator.title'),       'route' => 'tools.email-validator.index'],
            ],
            __('ui.group_references') => [
                ['name' => __('tools.port_reference.title'),    'route' => 'tools.port-reference.index'],
                ['name' => __('tools.osi_model.title'),          'route' => 'tools.osi-model.index'],
                ['name' => __('tools.http_status_codes.title'), 'route' => 'tools.http-status-codes.index'],
                ['name' => __('tools.rfc_browser.title'),        'route' => 'tools.rfc-browser.index'],
            ],
            __('ui.group_cabling') => [
                ['name' => __('tools.cable_schemas.title'), 'route' => 'tools.cable-schemas.index'],
                ['name' => __('tools.cable_colors.title'),  'route' => 'tools.cable-colors.index'],
            ],
            __('ui.group_tools') => [
                ['name' => __('tools.linux_cheatsheet.title'), 'route' => 'tools.linux-cheatsheet.index'],
                ['name' => __('tools.regex_tester.title'),     'route' => 'tools.regex-tester.index'],
                ['name' => __('tools.base_converter.title'),   'route' => 'tools.base-converter.index'],
                ['name' => __('tools.formatter.title'),        'route' => 'tools.formatter.index'],
                ['name' => __('tools.markdown_viewer.title'),  'route' => 'tools.markdown-viewer.index'],
            ],
        ];
    @endphp

    @foreach ($phases as $phase => $tools)
        <section class="mb-8">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">{{ $phase }}</h2>
            <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($tools as $tool)
                    @php($available = ! empty($tool['route']))
                    <li class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm {{ $available ? 'hover:border-emerald-400' : '' }}">
                        @if ($available)
                            <a href="{{ route($tool['route']) }}" class="block font-medium text-emerald-700 hover:underline">
                                {{ $tool['name'] }}
                            </a>
                        @else
                            <span class="block font-medium text-slate-800">{{ $tool['name'] }}</span>
                            <span class="mt-1 inline-block rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">
                                {{ __('ui.coming_soon') }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach
@endsection
