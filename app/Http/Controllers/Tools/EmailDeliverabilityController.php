<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\EmailDeliverabilityRequest;
use App\Tools\EmailDeliverabilityChecker\EmailDeliverabilityChecker;
use Illuminate\Contracts\View\View;

class EmailDeliverabilityController extends Controller
{
    public function index(): View
    {
        return view('tools.email-deliverability');
    }

    public function check(EmailDeliverabilityRequest $request): View
    {
        $checker = new EmailDeliverabilityChecker(
            $request->input('domain', ''),
            $request->input('dkim_selector') ?: null,
        );

        $result = $checker->check();

        return view('tools.partials.email-deliverability-result', ['result' => $result, 'validationErrors' => null]);
    }
}
