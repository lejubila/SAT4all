<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Cambia la lingua dell'interfaccia e la salva nella sessione.
     */
    public function switch(string $locale): RedirectResponse
    {
        if (in_array($locale, SetLocale::SUPPORTED, true)) {
            session(['locale' => $locale]);
        }

        return back();
    }
}
