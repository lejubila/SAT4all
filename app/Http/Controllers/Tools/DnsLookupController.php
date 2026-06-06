<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\DnsLookupRequest;
use App\Tools\DnsLookup\DnsLookup;
use Illuminate\Contracts\View\View;

class DnsLookupController extends Controller
{
    public function index(): View
    {
        return view('tools.dns-lookup', [
            'recordTypes' => DnsLookup::recordTypes(),
        ]);
    }

    public function lookup(DnsLookupRequest $request): View
    {
        $result = (new DnsLookup($request->validated()))->lookup();

        return view('tools.partials.dns-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
