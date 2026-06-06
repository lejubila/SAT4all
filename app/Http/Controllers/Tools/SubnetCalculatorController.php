<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\SubnetRequest;
use App\Tools\SubnetCalculator\SubnetCalculator;
use Illuminate\Contracts\View\View;

class SubnetCalculatorController extends Controller
{
    public function index(): View
    {
        return view('tools.subnet-calculator', [
            'prefixOptions' => SubnetCalculator::prefixOptions(),
        ]);
    }

    public function calculate(SubnetRequest $request): View
    {
        $result = (new SubnetCalculator($request->validated()))->calculate();

        return view('tools.partials.subnet-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
