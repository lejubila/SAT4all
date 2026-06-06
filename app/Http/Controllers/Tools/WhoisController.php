<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\WhoisRequest;
use App\Tools\Whois\Whois;
use Illuminate\View\View;

class WhoisController extends Controller
{
    public function index(): View
    {
        return view('tools.whois');
    }

    public function lookup(WhoisRequest $request): View
    {
        $result = (new Whois())->lookup($request->validated()['target']);

        return view('tools.partials.whois-result', [
            'errors' => null,
            'result' => $result,
            'target' => $request->validated()['target'],
        ]);
    }
}
