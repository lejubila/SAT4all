<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\HttpStatusCodes\HttpStatusCodes;
use Illuminate\View\View;

class HttpStatusCodesController extends Controller
{
    public function index(): View
    {
        $codes      = HttpStatusCodes::codes();
        $categories = HttpStatusCodes::categories();

        return view('tools.http-status-codes', compact('codes', 'categories'));
    }
}
