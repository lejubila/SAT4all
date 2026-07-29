<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\DnsLookupRequest;
use App\Tools\DnsLookup\DnsLookup;
use App\Tools\DnsLookup\DnsPropagation;
use Illuminate\Contracts\View\View;

class DnsLookupController extends Controller
{
    public function index(): View
    {
        return view('tools.dns-lookup', [
            'recordTypes'      => DnsLookup::recordTypes(),
            'propagationTypes' => DnsPropagation::supportedTypes(),
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

    public function propagation(DnsLookupRequest $request): View
    {
        $data   = $request->validated();
        $result = (new DnsPropagation)->check($data['host'], $data['type']);

        return view('tools.partials.dns-propagation-result', compact('result'));
    }
}
