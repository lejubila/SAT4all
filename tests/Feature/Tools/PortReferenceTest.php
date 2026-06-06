<?php

namespace Tests\Feature\Tools;

use App\Tools\PortReference\PortReference;
use Tests\TestCase;

class PortReferenceTest extends TestCase
{
    public function test_shows_reference_page_with_full_table(): void
    {
        $this->get(route('tools.port-reference.index'))
            ->assertOk()
            ->assertSee(__('tools.port_reference.title'))
            ->assertSee('SSH')
            ->assertSee('HTTPS')
            ->assertSee('MySQL');
    }

    public function test_search_by_port_number(): void
    {
        $this->get(route('tools.port-reference.lookup', ['q' => '443']))
            ->assertOk()
            ->assertSee('HTTPS')
            ->assertDontSee('PostgreSQL');
    }

    public function test_search_by_service_name(): void
    {
        $this->get(route('tools.port-reference.lookup', ['q' => 'ssh']))
            ->assertOk()
            ->assertSee('22')
            ->assertDontSee('MongoDB');
    }

    public function test_filter_by_protocol_udp(): void
    {
        $this->get(route('tools.port-reference.lookup', ['protocol' => 'udp']))
            ->assertOk()
            ->assertSee('SNMP')          // 161/udp
            ->assertDontSee('PostgreSQL'); // 5432/tcp
    }

    public function test_search_with_no_match_shows_message(): void
    {
        $this->get(route('tools.port-reference.lookup', ['q' => 'zzzznotaport']))
            ->assertOk()
            ->assertSee(__('tools.port_reference.no_results'));
    }

    public function test_logic_filters_by_port_substring(): void
    {
        $results = (new PortReference(['q' => '53']))->filter();
        $ports = array_column($results, 'port');

        $this->assertContains(53, $ports);  // DNS

        // Ogni risultato deve contenere la sottostringa "53" nel numero di porta.
        foreach ($ports as $port) {
            $this->assertStringContainsString('53', (string) $port);
        }
    }

    public function test_logic_protocol_filter_returns_only_matching(): void
    {
        $results = (new PortReference(['protocol' => 'udp']))->filter();

        foreach ($results as $entry) {
            $this->assertStringContainsString('udp', $entry['protocol']);
        }
        $this->assertNotEmpty($results);
    }

    public function test_logic_empty_filter_returns_full_dataset(): void
    {
        $this->assertSame(
            count(PortReference::all()),
            count((new PortReference([]))->filter())
        );
    }
}
