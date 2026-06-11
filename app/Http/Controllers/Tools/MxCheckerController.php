<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\MxCheckerRequest;
use App\Tools\MxChecker\MxChecker;
use Illuminate\Contracts\View\View;

class MxCheckerController extends Controller
{
    public function index(): View
    {
        $captcha = $this->generateCaptcha();

        return view('tools.mx-checker', compact('captcha'));
    }

    public function check(MxCheckerRequest $request): View
    {
        $result     = (new MxChecker($request->input('domain', '')))->check();
        $newCaptcha = $this->generateCaptcha();

        return view('tools.partials.mx-checker-result', [
            'result'           => $result,
            'validationErrors' => null,
            'newCaptcha'       => $newCaptcha,
        ]);
    }

    private function generateCaptcha(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code     = strtoupper(substr(str_shuffle($alphabet), 0, 6));
        session(['mx_checker_captcha' => $code]);

        return $code;
    }
}
