<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\OsiModel\OsiModel;
use Illuminate\Contracts\View\View;

class OsiModelController extends Controller
{
    public function index(): View
    {
        return view('tools.osi-model', [
            'layers'       => OsiModel::layers(),
            'colorClasses' => OsiModel::colorClasses(),
        ]);
    }
}
