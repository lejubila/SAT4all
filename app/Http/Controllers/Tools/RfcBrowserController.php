<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\RfcBrowser\RfcBrowser;
use Illuminate\View\View;

class RfcBrowserController extends Controller
{
    public function index(): View
    {
        $rfcs       = RfcBrowser::rfcs();
        $categories = RfcBrowser::categories();
        $statuses   = RfcBrowser::statuses();

        return view('tools.rfc-browser', compact('rfcs', 'categories', 'statuses'));
    }
}
