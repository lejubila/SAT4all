<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\MacLookupRequest;
use App\Tools\MacLookup\MacLookup;
use Illuminate\View\View;

class MacLookupController extends Controller
{
    public function index(): View
    {
        return view('tools.mac-lookup');
    }

    public function lookup(MacLookupRequest $request): View
    {
        $result = MacLookup::lookup($request->validated()['mac']);

        return view('tools.partials.mac-lookup-result', [
            'errors' => null,
            'result' => $result,
        ]);
    }
}
