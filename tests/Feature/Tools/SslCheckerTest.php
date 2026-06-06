<?php

namespace Tests\Feature\Tools;

use App\Tools\SslChecker\SslChecker;
use Tests\TestCase;

class SslCheckerTest extends TestCase
{
    public function test_shows_page(): void
    {
        $this->get(route('tools.ssl-checker.index'))
            ->assertOk()
            ->assertSee(__('tools.ssl_checker.title'));
    }

    public function test_empty_host_returns_error(): void
    {
        $this->post(route('tools.ssl-checker.check'), ['host' => ''])
            ->assertOk()
            ->assertSee(__('tools.ssl_checker.error_host_required'));
    }

    public function test_missing_host_returns_error(): void
    {
        $this->post(route('tools.ssl-checker.check'), [])
            ->assertOk()
            ->assertSee(__('tools.ssl_checker.error_host_required'));
    }

    public function test_invalid_port_returns_error(): void
    {
        $this->post(route('tools.ssl-checker.check'), [
            'host' => 'example.com',
            'port' => '99999',
        ])->assertOk()->assertSee(__('tools.ssl_checker.error_port_invalid'));
    }

    public function test_unreachable_host_returns_connection_error(): void
    {
        $this->post(route('tools.ssl-checker.check'), [
            'host' => 'this-host-does-not-exist-xyz.invalid',
            'port' => '443',
        ])->assertOk()->assertSee(__('tools.ssl_checker.status_error'));
    }

    public function test_class_err_returns_disconnected(): void
    {
        $checker = new SslChecker();
        $result  = $checker->check('this-host-does-not-exist-xyz.invalid', 443);
        $this->assertFalse($result['connected']);
        $this->assertNotEmpty($result['error']);
    }

    public function test_class_check_structure_on_error(): void
    {
        $checker = new SslChecker();
        $result  = $checker->check('no-such-host-xyz123.invalid', 443);
        $this->assertArrayHasKey('host', $result);
        $this->assertArrayHasKey('port', $result);
        $this->assertArrayHasKey('connected', $result);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_page_shows_form_inputs(): void
    {
        $this->get(route('tools.ssl-checker.index'))
            ->assertOk()
            ->assertSee('name="host"', false)
            ->assertSee('name="port"', false);
    }
}
