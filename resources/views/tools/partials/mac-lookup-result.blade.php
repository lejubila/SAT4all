@if ($errors && $errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
        @foreach ($errors->all() as $error)
            <p class="text-sm text-red-700">{{ $error }}</p>
        @endforeach
    </div>

@elseif ($result === null)
    <p class="text-sm text-slate-400">{{ __('tools.mac_lookup.empty') }}</p>

@else
    @php
        $isLocal  = $result['locally_administered'];
        $isMcast  = $result['multicast'];
        $ouiOnly  = $result['oui_only'];
    @endphp

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
            <span class="text-sm font-semibold text-slate-700">{{ __('tools.mac_lookup.result_title') }}</span>
            @if ($ouiOnly)
                <span class="ml-2 rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                    {{ __('tools.mac_lookup.oui_only_badge') }}
                </span>
            @endif
        </div>

        <div class="divide-y divide-slate-100">
            {{-- Vendor --}}
            <div class="flex items-start gap-4 px-4 py-3">
                <span class="w-44 shrink-0 text-sm font-medium text-slate-500">{{ __('tools.mac_lookup.field_vendor') }}</span>
                <div>
                    @if ($result['found'])
                        <span class="font-semibold text-emerald-700">{{ $result['vendor'] }}</span>
                    @else
                        <span class="italic text-slate-500">{{ __('tools.mac_lookup.vendor_unknown') }}</span>
                        <p class="mt-0.5 text-xs text-slate-400">{{ __('tools.mac_lookup.vendor_hint') }}</p>
                    @endif
                </div>
            </div>

            {{-- OUI --}}
            <div class="flex items-center gap-4 px-4 py-3">
                <span class="w-44 shrink-0 text-sm font-medium text-slate-500">{{ __('tools.mac_lookup.field_oui') }}</span>
                <span class="font-mono text-sm text-slate-800">{{ $result['oui'] }}</span>
            </div>

            {{-- NIC — only when full MAC was entered --}}
            @if (! $ouiOnly)
                <div class="flex items-center gap-4 px-4 py-3">
                    <span class="w-44 shrink-0 text-sm font-medium text-slate-500">{{ __('tools.mac_lookup.field_nic') }}</span>
                    <span class="font-mono text-sm text-slate-800">{{ $result['nic'] }}</span>
                </div>
            @endif

            {{-- Type --}}
            <div class="flex items-center gap-4 px-4 py-3">
                <span class="w-44 shrink-0 text-sm font-medium text-slate-500">{{ __('tools.mac_lookup.field_type') }}</span>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                                 {{ $isMcast ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $isMcast ? __('tools.mac_lookup.type_multicast') : __('tools.mac_lookup.type_unicast') }}
                    </span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                                 {{ $isLocal ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $isLocal ? __('tools.mac_lookup.type_local') : __('tools.mac_lookup.type_global') }}
                    </span>
                </div>
            </div>

            {{-- Formats — only when full MAC was entered --}}
            @if (! $ouiOnly)
                <div class="flex items-start gap-4 px-4 py-3">
                    <span class="w-44 shrink-0 text-sm font-medium text-slate-500">{{ __('tools.mac_lookup.field_formats') }}</span>
                    <div class="space-y-1">
                        @foreach (['colon' => 'AA:BB:CC:DD:EE:FF', 'dash' => 'AA-BB-CC-DD-EE-FF', 'dot' => 'AABB.CCDD.EEFF', 'plain' => 'AABBCCDDEEFF'] as $key => $label)
                            <div class="flex items-center gap-2">
                                <span class="w-36 font-mono text-sm text-slate-800">{{ $result['format'][$key] }}</span>
                                <span class="text-xs text-slate-400">({{ $label }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Virtual context card — only shown when detected --}}
    @php $virt = $result['virtual'] ?? null; @endphp
    @if ($virt !== null)
        @php
            $vType    = $virt['type'];
            $descKey  = 'tools.mac_lookup.virtual_desc_' . $vType;
            $usesKey  = 'tools.mac_lookup.virtual_uses_' . $vType;
            $usesList = explode('|', __($usesKey));
            $certain  = $virt['certain'];
        @endphp
        <div class="mt-4 rounded-lg border {{ $certain ? 'border-violet-200 bg-violet-50' : 'border-amber-200 bg-amber-50' }} p-4 shadow-sm">
            <div class="mb-3 flex items-center gap-2">
                <span class="text-sm font-semibold {{ $certain ? 'text-violet-700' : 'text-amber-700' }}">
                    {{ __('tools.mac_lookup.virtual_section') }}
                </span>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold
                             {{ $certain ? 'bg-violet-200 text-violet-800' : 'bg-amber-200 text-amber-800' }}">
                    {{ $certain ? __('tools.mac_lookup.virtual_badge_certain') : __('tools.mac_lookup.virtual_badge_probable') }}
                </span>
                @if ($virt['platform'])
                    <span class="ml-auto font-mono text-xs {{ $certain ? 'text-violet-600' : 'text-amber-600' }}">
                        {{ $virt['platform'] }}
                    </span>
                @endif
            </div>

            <p class="mb-3 text-sm {{ $certain ? 'text-violet-800' : 'text-amber-800' }}">
                {{ __($descKey) }}
            </p>

            <div>
                <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide {{ $certain ? 'text-violet-600' : 'text-amber-600' }}">
                    {{ __('tools.mac_lookup.virtual_uses_label') }}
                </p>
                <ul class="space-y-1">
                    @foreach ($usesList as $use)
                        <li class="flex items-start gap-2 text-sm {{ $certain ? 'text-violet-700' : 'text-amber-700' }}">
                            <span class="mt-0.5 shrink-0 text-xs">›</span>
                            <span>{{ $use }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endif
