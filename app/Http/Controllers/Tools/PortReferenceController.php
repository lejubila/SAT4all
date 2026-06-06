<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\PortReferenceRequest;
use App\Tools\PortReference\PortReference;
use Illuminate\Contracts\View\View;

class PortReferenceController extends Controller
{
    public function index(): View
    {
        return view('tools.port-reference', [
            'protocols' => PortReference::protocols(),
            'results'   => PortReference::all(),
        ]);
    }

    public function lookup(PortReferenceRequest $request): View
    {
        $results = (new PortReference($request->validated()))->filter();

        return view('tools.partials.port-reference-result', [
            'results' => $results,
        ]);
    }
}
