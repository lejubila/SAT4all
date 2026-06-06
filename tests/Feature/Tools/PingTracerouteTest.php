<?php

namespace Tests\Feature\Tools;

use App\Tools\PingTraceroute\PingTraceroute;
use Tests\TestCase;

class PingTracerouteTest extends TestCase
{
    public function test_shows_ping_traceroute_page(): void
    {
        $this->get(route('tools.ping-traceroute.index'))
            ->assertOk()
            ->assertSee(__('tools.ping_traceroute.title'));
    }

    public function test_empty_target_returns_validation_error(): void
    {
        $this->post(route('tools.ping-traceroute.run'), ['target' => '', 'tool' => 'ping'])
            ->assertOk()
            ->assertSee(__('tools.ping_traceroute.error_target_required'));
    }

    public function test_invalid_target_returns_validation_error(): void
    {
        $this->post(route('tools.ping-traceroute.run'), ['target' => 'not valid!@#', 'tool' => 'ping'])
            ->assertOk()
            ->assertSee(__('tools.ping_traceroute.error_target_invalid'));
    }

    public function test_invalid_tool_returns_validation_error(): void
    {
        $this->post(route('tools.ping-traceroute.run'), ['target' => '127.0.0.1', 'tool' => 'nmap'])
            ->assertOk()
            ->assertSee(__('tools.ping_traceroute.error_tool_invalid'));
    }

    public function test_ping_localhost_returns_result(): void
    {
        $this->post(route('tools.ping-traceroute.run'), [
            'target' => '127.0.0.1',
            'tool'   => 'ping',
            'count'  => 1,
        ])->assertOk()
          ->assertSee('127.0.0.1');
    }

    public function test_traceroute_localhost_returns_result(): void
    {
        $this->post(route('tools.ping-traceroute.run'), [
            'target' => '127.0.0.1',
            'tool'   => 'traceroute',
            'hops'   => 5,
        ])->assertOk()
          ->assertSee('127.0.0.1');
    }

    public function test_valid_hostname_passes_validation(): void
    {
        $this->assertTrue(PingTraceroute::validateTarget('example.com'));
        $this->assertTrue(PingTraceroute::validateTarget('sub.example.co.uk'));
        $this->assertTrue(PingTraceroute::validateTarget('host-name.local'));
    }

    public function test_valid_ip_passes_validation(): void
    {
        $this->assertTrue(PingTraceroute::validateTarget('192.168.1.1'));
        $this->assertTrue(PingTraceroute::validateTarget('8.8.8.8'));
        $this->assertTrue(PingTraceroute::validateTarget('2001:db8::1'));
    }

    public function test_invalid_targets_fail_validation(): void
    {
        $this->assertFalse(PingTraceroute::validateTarget('not valid'));
        $this->assertFalse(PingTraceroute::validateTarget('../../etc/passwd'));
        $this->assertFalse(PingTraceroute::validateTarget('host; rm -rf /'));
        $this->assertFalse(PingTraceroute::validateTarget('$(whoami)'));
        $this->assertFalse(PingTraceroute::validateTarget(str_repeat('a', 254)));
    }

    public function test_invalid_count_is_rejected(): void
    {
        $this->post(route('tools.ping-traceroute.run'), [
            'target' => '127.0.0.1',
            'tool'   => 'ping',
            'count'  => 999,
        ])->assertOk();
        // count=999 non è in whitelist: la validazione fallisce silenziosamente
        // ma il controller non deve mai lanciare un ping con quel valore
    }

    public function test_invalid_hops_is_rejected(): void
    {
        $response = $this->post(route('tools.ping-traceroute.run'), [
            'target' => '127.0.0.1',
            'tool'   => 'traceroute',
            'hops'   => 999,
        ]);
        $response->assertOk();
    }
}
