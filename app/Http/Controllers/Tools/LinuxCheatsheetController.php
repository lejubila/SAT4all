<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Tools\LinuxCheatsheet\LinuxCheatsheet;
use Illuminate\Contracts\View\View;

class LinuxCheatsheetController extends Controller
{
    public function index(): View
    {
        $categories = array_map(
            fn (string $key, array $cmds) => [
                'key'      => $key,
                'label'    => __("tools.linux_cheatsheet.cat_{$key}"),
                'commands' => $cmds,
            ],
            array_keys(LinuxCheatsheet::categories()),
            array_values(LinuxCheatsheet::categories())
        );

        return view('tools.linux-cheatsheet', [
            'categories' => array_values($categories),
        ]);
    }
}
