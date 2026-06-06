<?php

namespace Tests\Feature\Tools;

use App\Tools\DnsLookup\DnsLookup;
use Tests\TestCase;

class DnsLookupTest extends TestCase
{
    public function test_shows_lookup_page(): void
    {
        $this->get(route('tools.dns-lookup.index'))
            ->assertOk()
            ->assertSee(__('tools.dns_lookup.title'))
            ->assertSee('A')
            ->assertSee('MX')
            ->assertSee('TXT');
    }

    public function test_empty_host_returns_validation_error(): void
    {
        $this->post(route('tools.dns-lookup.lookup'), ['host' => '', 'type' => 'A'])
            ->assertOk()
            ->assertSee(__('tools.dns_lookup.error_host_required'));
    }

    public function test_invalid_host_returns_validation_error(): void
    {
        $this->post(route('tools.dns-lookup.lookup'), ['host' => 'not a valid host!!', 'type' => 'A'])
            ->assertOk()
            ->assertSee(__('tools.dns_lookup.error_host_invalid'));
    }

    public function test_invalid_record_type_returns_validation_error(): void
    {
        $this->post(route('tools.dns-lookup.lookup'), ['host' => 'example.com', 'type' => 'INVALID'])
            ->assertOk()
            ->assertSee(__('tools.dns_lookup.error_type_invalid'));
    }

    public function test_valid_request_returns_result_partial(): void
    {
        // La risposta deve essere 200 con la struttura della partial,
        // indipendentemente da quanti record restituisce la rete.
        $this->post(route('tools.dns-lookup.lookup'), ['host' => 'example.com', 'type' => 'A'])
            ->assertOk()
            ->assertSee('example.com');
    }

    public function test_record_types_list_is_complete(): void
    {
        $types = DnsLookup::recordTypes();

        foreach (['A', 'AAAA', 'MX', 'NS', 'TXT', 'CNAME', 'SOA', 'PTR', 'ALL'] as $expected) {
            $this->assertContains($expected, $types);
        }
    }

    public function test_ip_accepted_for_ptr_query(): void
    {
        $this->post(route('tools.dns-lookup.lookup'), ['host' => '8.8.8.8', 'type' => 'PTR'])
            ->assertOk();
    }

    public function test_throttle_middleware_is_applied_to_lookup_route(): void
    {
        $route = collect(app('router')->getRoutes())->first(
            fn ($r) => $r->getName() === 'tools.dns-lookup.lookup'
        );

        $this->assertNotNull($route);
        $this->assertContains('throttle:dns-lookup', $route->middleware());
    }

    public function test_lookup_returns_expected_structure(): void
    {
        $result = (new DnsLookup(['host' => 'example.com', 'type' => 'A']))->lookup();

        $this->assertArrayHasKey('host', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('records', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertSame('example.com', $result['host']);
        $this->assertSame('A', $result['type']);
        $this->assertIsArray($result['records']);
        $this->assertSame(count($result['records']), $result['count']);
    }
}
