@extends('layouts.app')

@section('title', __('tools.bandwidth_calculator.title'))

@section('content')
    <section class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('tools.bandwidth_calculator.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-slate-600">
            {{ __('tools.bandwidth_calculator.description') }}
        </p>
    </section>

    <div x-data="bwCalc">

        <form hx-post="{{ route('tools.bandwidth-calculator.calculate') }}"
              hx-target="#result"
              hx-swap="innerHTML"
              class="space-y-5">
            @csrf
            <input type="hidden" name="mode" :value="mode">

            {{-- Mode tabs --}}
            <div class="flex flex-wrap gap-2">
                @foreach ([
                    'time'      => 'mode_time',
                    'size'      => 'mode_size',
                    'bandwidth' => 'mode_bandwidth',
                ] as $m => $key)
                    <button type="button"
                            @click="mode = '{{ $m }}'"
                            :class="mode === '{{ $m }}'
                                ? 'bg-emerald-600 text-white border-emerald-600'
                                : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                            class="rounded-lg border px-4 py-2 text-sm font-medium transition-colors">
                        {{ __('tools.bandwidth_calculator.' . $key) }}
                    </button>
                @endforeach
            </div>

            {{-- File size row --}}
            <div x-show="mode === 'time' || mode === 'bandwidth'">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.bandwidth_calculator.label_file_size') }}
                </label>
                <div class="flex gap-2">
                    <input type="number" name="size_value" min="0" step="any"
                           placeholder="0"
                           class="w-40 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <select name="size_unit"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @foreach (array_keys(\App\Tools\BandwidthCalculator\BandwidthCalculator::SIZE_UNITS) as $u)
                            <option value="{{ $u }}" @selected($u === 'GB')>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Bandwidth row --}}
            <div x-show="mode === 'time' || mode === 'size'">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.bandwidth_calculator.label_bandwidth') }}
                </label>
                <div class="flex gap-2">
                    <input type="number" name="bw_value" min="0" step="any"
                           placeholder="0"
                           class="w-40 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <select name="bw_unit"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @foreach (array_keys(\App\Tools\BandwidthCalculator\BandwidthCalculator::BW_UNITS) as $u)
                            <option value="{{ $u }}" @selected($u === 'Mbps')>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Time row --}}
            <div x-show="mode === 'size' || mode === 'bandwidth'">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.bandwidth_calculator.label_time') }}
                </label>
                <div class="flex gap-2">
                    <input type="number" name="time_value" min="0" step="any"
                           placeholder="0"
                           class="w-40 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <select name="time_unit"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @foreach (array_keys(\App\Tools\BandwidthCalculator\BandwidthCalculator::TIME_UNITS) as $u)
                            <option value="{{ $u }}" @selected($u === 'h')>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Overhead --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    {{ __('tools.bandwidth_calculator.label_overhead') }}
                </label>
                <input type="number" name="overhead" min="0" max="99" step="0.1"
                       value="0"
                       placeholder="{{ __('tools.bandwidth_calculator.placeholder_overhead') }}"
                       class="w-32 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                {{ __('tools.bandwidth_calculator.btn_calculate') }}
            </button>
        </form>

        <div id="result" class="mt-6"></div>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bwCalc', () => ({
        mode: 'time',
    }))
})
</script>
@endpush
