<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\BaseConverterRequest;
use App\Tools\BaseConverter\BaseConverter;
use Illuminate\View\View;

class BaseConverterController extends Controller
{
    public function index(): View
    {
        return view('tools.base-converter');
    }

    public function convert(BaseConverterRequest $request): View
    {
        $number   = $request->input('number') ?? '';
        $fromBase = (int) $request->input('from_base', 10);

        $result = (new BaseConverter())->convert($number, $fromBase);

        return view('tools.partials.base-converter-result', compact('result'));
    }
}
