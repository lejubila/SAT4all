<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\VlanRequest;
use App\Tools\SubnetCalculator\SubnetCalculator;
use App\Tools\VlanCalculator\VlanCalculator;
use Illuminate\Contracts\View\View;

class VlanCalculatorController extends Controller
{
    public function index(): View
    {
        return view('tools.vlan-calculator', [
            'baseCidrOptions'   => SubnetCalculator::prefixOptions(),
            'subnetCidrOptions' => SubnetCalculator::prefixOptions(),
        ]);
    }

    public function calculate(VlanRequest $request): View
    {
        $result = (new VlanCalculator($request->validated()))->calculate();

        return view('tools.partials.vlan-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
