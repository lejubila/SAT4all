<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\CableColors\CableColors;
use Illuminate\Contracts\View\View;

class CableColorsController extends Controller
{
    public function index(): View
    {
        return view('tools.cable-colors', [
            'standards'    => CableColors::standards(),
            'differingPins'=> CableColors::differingPins(),
            'pairs'        => CableColors::pairs(),
        ]);
    }
}
