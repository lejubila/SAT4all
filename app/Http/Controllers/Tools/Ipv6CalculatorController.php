<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\Ipv6Request;
use App\Tools\Ipv6Calculator\Ipv6Calculator;
use Illuminate\Contracts\View\View;

class Ipv6CalculatorController extends Controller
{
    public function index(): View
    {
        return view('tools.ipv6-calculator', [
            'prefixOptions' => Ipv6Calculator::prefixOptions(),
        ]);
    }

    public function calculate(Ipv6Request $request): View
    {
        $result = (new Ipv6Calculator($request->validated()))->calculate();

        return view('tools.partials.ipv6-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
