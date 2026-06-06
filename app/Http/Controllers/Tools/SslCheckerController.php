<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\SslCheckerRequest;
use App\Tools\SslChecker\SslChecker;
use Illuminate\View\View;

class SslCheckerController extends Controller
{
    public function index(): View
    {
        return view('tools.ssl-checker');
    }

    public function check(SslCheckerRequest $request): View
    {
        $host   = trim($request->input('host'));
        $port   = (int) ($request->input('port') ?? 443);
        $port   = $port ?: 443;

        $result = (new SslChecker())->check($host, $port);

        return view('tools.partials.ssl-checker-result', compact('result'));
    }
}
