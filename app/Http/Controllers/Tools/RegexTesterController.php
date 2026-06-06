<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\RegexTesterRequest;
use App\Tools\RegexTester\RegexTester;
use Illuminate\View\View;

class RegexTesterController extends Controller
{
    public function index(): View
    {
        return view('tools.regex-tester');
    }

    public function test(RegexTesterRequest $request): View
    {
        $pattern     = $request->input('pattern') ?? '';
        $flags       = $request->input('flags') ?? '';
        $subject     = $request->input('subject') ?? '';
        $replacement = $request->input('replacement');

        $tester = new RegexTester();
        $result = $tester->test($pattern, $flags, $subject);

        if (! empty($result['idle']) || ! $result['valid']) {
            return view('tools.partials.regex-tester-result', compact('result'));
        }

        if ($replacement !== null && $replacement !== '') {
            $replaced = $tester->replace($pattern, $flags, $subject, $replacement);
            $result['replacement'] = $replaced;
        }

        return view('tools.partials.regex-tester-result', compact('result'));
    }
}
