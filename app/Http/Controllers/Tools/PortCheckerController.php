<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\PortCheckerRequest;
use App\Tools\PortChecker\PortChecker;
use Illuminate\View\View;

class PortCheckerController extends Controller
{
    public function index(): View
    {
        $captcha = $this->generateCaptcha();

        return view('tools.port-checker', compact('captcha'));
    }

    public function check(PortCheckerRequest $request): View
    {
        $validated = $request->validated();

        $result = (new PortChecker())->check(
            $validated['host'],
            (int) $validated['port'],
            $validated['protocol'],
        );

        $newCaptcha = $this->generateCaptcha();

        return view('tools.partials.port-checker-result', compact('result', 'newCaptcha'));
    }

    private function generateCaptcha(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code     = strtoupper(substr(str_shuffle($alphabet), 0, 6));
        session(['port_checker_captcha' => $code]);

        return $code;
    }
}
