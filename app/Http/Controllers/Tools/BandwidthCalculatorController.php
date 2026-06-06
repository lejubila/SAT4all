<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\BandwidthCalculatorRequest;
use App\Tools\BandwidthCalculator\BandwidthCalculator;
use Illuminate\View\View;

class BandwidthCalculatorController extends Controller
{
    public function index(): View
    {
        return view('tools.bandwidth-calculator');
    }

    public function calculate(BandwidthCalculatorRequest $request): View
    {
        $calc = new BandwidthCalculator();

        $result = $calc->calculate(
            mode:      $request->input('mode', 'time'),
            sizeValue: (float) ($request->input('size_value') ?? 0),
            sizeUnit:  $request->input('size_unit', 'GB'),
            bwValue:   (float) ($request->input('bw_value') ?? 0),
            bwUnit:    $request->input('bw_unit', 'Mbps'),
            timeValue: (float) ($request->input('time_value') ?? 0),
            timeUnit:  $request->input('time_unit', 'h'),
            overhead:  (float) ($request->input('overhead') ?? 0),
        );

        // Attach extra display tables to the result
        if ($result['valid'] ?? false) {
            $mode = $result['mode'];
            if ($mode === 'time' || $mode === 'bandwidth') {
                $result['size_table'] = $calc->sizeTable(
                    (float) ($request->input('size_value') ?? 0)
                    * (BandwidthCalculator::SIZE_UNITS[$request->input('size_unit', 'GB')] ?? 1)
                );
            }
            if ($mode === 'size') {
                $result['size_table'] = $calc->sizeTable($result['result']['bytes']);
            }
            if ($mode === 'bandwidth') {
                $result['bw_table'] = $calc->bwTable($result['result']['bps']);
            }
            if ($mode === 'time') {
                $result['bw_table']   = $calc->bwTable(
                    (float) ($request->input('bw_value') ?? 0)
                    * (BandwidthCalculator::BW_UNITS[$request->input('bw_unit', 'Mbps')] ?? 1)
                );
            }
        }

        return view('tools.partials.bandwidth-calculator-result', compact('result'));
    }
}
