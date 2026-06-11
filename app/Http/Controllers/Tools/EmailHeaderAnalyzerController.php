<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\EmailHeaderAnalyzerRequest;
use App\Tools\EmailHeaderAnalyzer\EmailHeaderAnalyzer;
use Illuminate\Contracts\View\View;

class EmailHeaderAnalyzerController extends Controller
{
    public function index(): View
    {
        return view('tools.email-header-analyzer');
    }

    public function analyze(EmailHeaderAnalyzerRequest $request): View
    {
        $input  = (string) ($request->input('header') ?? '');
        $result = (new EmailHeaderAnalyzer($input))->analyze();

        return view('tools.partials.email-header-analyzer-result', compact('result'));
    }
}
