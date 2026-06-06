<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\CidrCheatsheet\CidrCheatsheet;
use Illuminate\Contracts\View\View;

class CidrCheatsheetController extends Controller
{
    public function index(): View
    {
        return view('tools.cidr-cheatsheet', [
            'rows' => CidrCheatsheet::rows(),
        ]);
    }
}
