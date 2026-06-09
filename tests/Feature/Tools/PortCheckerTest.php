<?php

namespace Tests\Feature\Tools;

use App\Tools\PortChecker\PortChecker;
use Tests\TestCase;

class PortCheckerTest extends TestCase
{
    // ── Page ────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.port-checker.index'))
            ->assertOk()
            ->assertSee(__('tools.port_checker.title'));
    }

    public function test_page_generates_captcha_in_session(): void
    {
        $this->get(route('tools.port-checker.index'));
        $this->assertNotNull(session('port_checker_captcha'));
        $this->assertSame(6, strlen(session('port_checker_captcha')));
    }

    // ── Captcha validation ──────────────────────────────────────────────────

    public function test_wrong_captcha_returns_error(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'          => 'example.com',
            'port'          => 80,
            'protocol'      => 'tcp',
            'captcha_input' => 'ZZZZZZ',
        ])->assertOk()
          ->assertSee(__('tools.port_checker.error_captcha'));
    }

    public function test_missing_captcha_returns_error(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'     => 'example.com',
            'port'     => 80,
            'protocol' => 'tcp',
        ])->assertOk()
          ->assertSee(__('tools.port_checker.error_captcha_required'));
    }

    public function test_captcha_is_case_insensitive(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        // Should NOT return captcha error (may return other errors or result)
        $response = $this->post(route('tools.port-checker.check'), [
            'host'          => '127.0.0.1',
            'port'          => 80,
            'protocol'      => 'tcp',
            'captcha_input' => 'abcdef',
        ])->assertOk();

        $response->assertDontSee(__('tools.port_checker.error_captcha'));
    }

    public function test_captcha_is_regenerated_after_submit(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'          => 'example.com',
            'port'          => 80,
            'protocol'      => 'tcp',
            'captcha_input' => 'ZZZZZZ',
        ]);

        $this->assertNotNull(session('port_checker_captcha'));
        $this->assertNotSame('ABCDEF', session('port_checker_captcha'));
    }

    // ── Input validation ────────────────────────────────────────────────────

    public function test_invalid_host_returns_error(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'          => 'not a valid host!!!',
            'port'          => 80,
            'protocol'      => 'tcp',
            'captcha_input' => 'ABCDEF',
        ])->assertOk()
          ->assertSee(__('tools.port_checker.error_host_invalid'));
    }

    public function test_port_out_of_range_returns_error(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'          => 'example.com',
            'port'          => 99999,
            'protocol'      => 'tcp',
            'captcha_input' => 'ABCDEF',
        ])->assertOk()
          ->assertSee(__('tools.port_checker.error_port_invalid'));
    }

    // ── TCP check ───────────────────────────────────────────────────────────

    public function test_tcp_open_port_returns_open_status(): void
    {
        // PHP-FPM listens on 9000 inside its own container
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'          => '127.0.0.1',
            'port'          => 9000,
            'protocol'      => 'tcp',
            'captcha_input' => 'ABCDEF',
        ])->assertOk()
          ->assertSee(__('tools.port_checker.status_open'));
    }

    public function test_tcp_closed_port_returns_non_open_status(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $response = $this->post(route('tools.port-checker.check'), [
            'host'          => '127.0.0.1',
            'port'          => 19999,
            'protocol'      => 'tcp',
            'captcha_input' => 'ABCDEF',
        ])->assertOk();

        // Must show closed or filtered (never open)
        $response->assertDontSee(__('tools.port_checker.status_open'));
    }

    // ── UDP check ───────────────────────────────────────────────────────────

    public function test_udp_check_returns_valid_response(): void
    {
        $this->withSession(['port_checker_captcha' => 'ABCDEF']);

        $this->post(route('tools.port-checker.check'), [
            'host'          => '127.0.0.1',
            'port'          => 53,
            'protocol'      => 'udp',
            'captcha_input' => 'ABCDEF',
        ])->assertOk()
          ->assertSee(__('tools.port_checker.udp_note'));
    }

    // ── PortChecker class ───────────────────────────────────────────────────

    public function test_validate_host_accepts_ipv4(): void
    {
        $this->assertTrue(PortChecker::validateHost('192.168.1.1'));
    }

    public function test_validate_host_accepts_ipv6(): void
    {
        $this->assertTrue(PortChecker::validateHost('::1'));
    }

    public function test_validate_host_accepts_hostname(): void
    {
        $this->assertTrue(PortChecker::validateHost('example.com'));
    }

    public function test_validate_host_rejects_invalid(): void
    {
        $this->assertFalse(PortChecker::validateHost('not valid!!!'));
        $this->assertFalse(PortChecker::validateHost(''));
        $this->assertFalse(PortChecker::validateHost(str_repeat('a', 254)));
    }
}
