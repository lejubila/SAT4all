@if (! empty($validationErrors) && $validationErrors->isNotEmpty())
    <div class="rounded-lg border border-red-200 bg-red-50 p-5">
        <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($validationErrors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@elseif (! empty($result))
    @if ($result['error'])
        <div class="rounded-lg border border-red-200 bg-red-50 p-5 text-sm text-red-700">
            {{ $result['error'] }}
        </div>
    @else
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">
                {{ __('tools.ip_geolocation.result_title') }}
            </h2>

            @php
                $rows = [
                    'field_ip'       => $result['ip'],
                    'field_country'  => $result['country'].($result['country_code'] ? ' ('.$result['country_code'].')' : ''),
                    'field_region'   => $result['region'],
                    'field_city'     => $result['city'].($result['zip'] ? ' — '.$result['zip'] : ''),
                    'field_timezone' => $result['timezone'],
                    'field_isp'      => $result['isp'],
                    'field_org'      => $result['org'],
                    'field_as'       => $result['as'],
                ];
            @endphp

            <dl class="divide-y divide-slate-100">
                @foreach ($rows as $key => $value)
                    @if ($value)
                        <div class="flex items-start justify-between gap-4 py-2">
                            <dt class="shrink-0 text-sm text-slate-500">{{ __('tools.ip_geolocation.'.$key) }}</dt>
                            <dd class="text-right text-sm font-medium text-slate-800">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach

                {{-- Coordinate con link OpenStreetMap --}}
                <div class="flex items-start justify-between gap-4 py-2">
                    <dt class="shrink-0 text-sm text-slate-500">{{ __('tools.ip_geolocation.field_coords') }}</dt>
                    <dd class="text-right text-sm font-medium text-slate-800">
                        @if ($result['lat'] !== null && $result['lon'] !== null)
                            {{ $result['lat'] }}, {{ $result['lon'] }}
                            <a href="https://www.openstreetmap.org/?mlat={{ $result['lat'] }}&mlon={{ $result['lon'] }}&zoom=10"
                               target="_blank" rel="noopener noreferrer"
                               class="ml-2 text-xs text-emerald-600 hover:underline">
                                ↗ {{ __('tools.ip_geolocation.open_map') }}
                            </a>
                        @else
                            <span class="text-slate-400">{{ __('tools.ip_geolocation.no_coords') }}</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    @endif
@endif
