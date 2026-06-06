<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\CableSchemas\CableSchemas;
use Illuminate\Contracts\View\View;

class CableSchemasController extends Controller
{
    public function index(): View
    {
        return view('tools.cable-schemas', [
            'standards' => CableSchemas::standards(),
            'cables'    => CableSchemas::cables(),
        ]);
    }
}
