<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\EmailValidatorRequest;
use App\Tools\EmailValidator\EmailValidator;
use Illuminate\View\View;

class EmailValidatorController extends Controller
{
    public function index(): View
    {
        $captcha = $this->generateCaptcha();
        return view('tools.email-validator', compact('captcha'));
    }

    public function check(EmailValidatorRequest $request): View
    {
        $result     = (new EmailValidator($request->input('email', '')))->validate();
        $newCaptcha = $this->generateCaptcha();

        return view('tools.partials.email-validator-result', [
            'result'           => $result,
            'validationErrors' => null,
            'newCaptcha'       => $newCaptcha,
        ]);
    }

    private function generateCaptcha(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code     = strtoupper(substr(str_shuffle($alphabet), 0, 6));
        session(['email_validator_captcha' => $code]);
        return $code;
    }
}
