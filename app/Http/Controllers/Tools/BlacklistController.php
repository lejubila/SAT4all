<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\BlacklistRequest;
use App\Tools\BlacklistChecker\BlacklistChecker;
use Illuminate\Contracts\View\View;

class BlacklistController extends Controller
{
    public function index(): View
    {
        return view('tools.blacklist-checker');
    }

    public function check(BlacklistRequest $request): View
    {
        $result = (new BlacklistChecker($request->input('target', '')))->check();

        return view('tools.partials.blacklist-checker-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
