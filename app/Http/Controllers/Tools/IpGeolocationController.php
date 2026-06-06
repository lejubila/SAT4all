<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tools\IpGeolocationRequest;
use App\Tools\IpGeolocation\IpGeolocation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IpGeolocationController extends Controller
{
    public function index(): View
    {
        return view('tools.ip-geolocation');
    }

    public function lookup(IpGeolocationRequest $request): View
    {
        $ip = $request->validated()['ip'];

        $result = Cache::remember("geo:{$ip}", 3600, function () use ($ip): array {
            $response = Http::timeout(8)->get(
                IpGeolocation::ENDPOINT.$ip,
                ['fields' => IpGeolocation::FIELDS]
            );

            if ($response->failed()) {
                return IpGeolocation::normalize([
                    'status'  => 'fail',
                    'message' => __('tools.ip_geolocation.error_api_unavailable'),
                    'query'   => $ip,
                ]);
            }

            return IpGeolocation::normalize($response->json());
        });

        return view('tools.partials.ip-geolocation-result', [
            'result'           => $result,
            'validationErrors' => null,
        ]);
    }
}
