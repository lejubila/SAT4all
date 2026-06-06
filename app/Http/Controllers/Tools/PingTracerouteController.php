<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\PingTracerouteRequest;
use App\Tools\PingTraceroute\PingTraceroute;
use Illuminate\Contracts\View\View;

class PingTracerouteController extends Controller
{
    public function index(): View
    {
        return view('tools.ping-traceroute');
    }

    public function run(PingTracerouteRequest $request): View
    {
        $data   = $request->validated();
        $tool   = new PingTraceroute();
        $target = $data['target'];

        set_time_limit(120);

        $result = match ($data['tool']) {
            'traceroute' => $tool->traceroute($target, (int) ($data['hops'] ?? 20)),
            default      => $tool->ping($target, (int) ($data['count'] ?? 4)),
        };

        $result['target'] = $target;
        $result['tool']   = $data['tool'];

        return view('tools.partials.ping-traceroute-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
