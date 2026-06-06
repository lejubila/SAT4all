<?php

namespace Tests\Feature\Tools;

use App\Tools\IpGeolocation\IpGeolocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IpGeolocationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_shows_geolocation_page(): void
    {
        $this->get(route('tools.ip-geolocation.index'))
            ->assertOk()
            ->assertSee(__('tools.ip_geolocation.title'));
    }

    public function test_empty_ip_returns_validation_error(): void
    {
        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => ''])
            ->assertOk()
            ->assertSee(__('tools.ip_geolocation.error_ip_required'));
    }

    public function test_invalid_ip_returns_validation_error(): void
    {
        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => 'not-an-ip'])
            ->assertOk()
            ->assertSee(__('tools.ip_geolocation.error_ip_invalid'));
    }

    public function test_successful_lookup_shows_result(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response([
                'status'      => 'success',
                'query'       => '8.8.8.8',
                'country'     => 'United States',
                'countryCode' => 'US',
                'regionName'  => 'California',
                'region'      => 'CA',
                'city'        => 'Mountain View',
                'zip'         => '94043',
                'lat'         => 37.4056,
                'lon'         => -122.0775,
                'timezone'    => 'America/Los_Angeles',
                'isp'         => 'Google LLC',
                'org'         => 'Google Public DNS',
                'as'          => 'AS15169 Google LLC',
            ], 200),
        ]);

        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => '8.8.8.8'])
            ->assertOk()
            ->assertSee('8.8.8.8')
            ->assertSee('United States')
            ->assertSee('Mountain View')
            ->assertSee('Google LLC')
            ->assertSee('37.4056');
    }

    public function test_api_failure_shows_error_message(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response([
                'status'  => 'fail',
                'message' => 'private range',
                'query'   => '192.168.1.1',
            ], 200),
        ]);

        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => '192.168.1.1'])
            ->assertOk()
            ->assertSee('private range');
    }

    public function test_api_unavailable_shows_error(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response(null, 503),
        ]);

        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => '1.1.1.1'])
            ->assertOk()
            ->assertSee(__('tools.ip_geolocation.error_api_unavailable'));
    }

    public function test_result_is_cached(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response([
                'status'      => 'success',
                'query'       => '1.1.1.1',
                'country'     => 'Australia',
                'countryCode' => 'AU',
                'regionName'  => 'Queensland',
                'region'      => 'QLD',
                'city'        => 'South Brisbane',
                'zip'         => '4101',
                'lat'         => -27.4748,
                'lon'         => 153.017,
                'timezone'    => 'Australia/Brisbane',
                'isp'         => 'APNIC',
                'org'         => 'APNIC Research',
                'as'          => 'AS13335 Cloudflare',
            ], 200),
        ]);

        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => '1.1.1.1']);
        $this->post(route('tools.ip-geolocation.lookup'), ['ip' => '1.1.1.1']);

        // L'API deve essere stata chiamata una sola volta grazie alla cache.
        Http::assertSentCount(1);
    }

    public function test_normalize_builds_correct_structure(): void
    {
        $result = IpGeolocation::normalize([
            'status'      => 'success',
            'query'       => '8.8.8.8',
            'country'     => 'United States',
            'countryCode' => 'US',
            'regionName'  => 'California',
            'region'      => 'CA',
            'city'        => 'Mountain View',
            'zip'         => '94043',
            'lat'         => 37.4056,
            'lon'         => -122.0775,
            'timezone'    => 'America/Los_Angeles',
            'isp'         => 'Google LLC',
            'org'         => 'Google Public DNS',
            'as'          => 'AS15169 Google LLC',
        ]);

        $this->assertSame('8.8.8.8', $result['ip']);
        $this->assertSame('United States', $result['country']);
        $this->assertSame('Mountain View', $result['city']);
        $this->assertSame(37.4056, $result['lat']);
        $this->assertNull($result['error']);
    }
}
