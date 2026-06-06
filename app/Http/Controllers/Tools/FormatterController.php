<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\FormatterRequest;
use App\Tools\Formatter\Formatter;
use Illuminate\View\View;

class FormatterController extends Controller
{
    public function index(): View
    {
        return view('tools.formatter');
    }

    public function format(FormatterRequest $request): View
    {
        $input  = $request->input('input') ?? '';
        $format = $request->input('format', 'auto');
        $indent = (int) ($request->input('indent') ?? 4);

        $result = (new Formatter())->format($input, $format, $indent);

        return view('tools.partials.formatter-result', compact('result'));
    }
}
