<?php

namespace Tests\Feature\Tools;

use App\Tools\Whois\Whois;
use Tests\TestCase;

class WhoisTest extends TestCase
{
    public function test_shows_whois_page(): void
    {
        $this->get(route('tools.whois.index'))
            ->assertOk()
            ->assertSee(__('tools.whois.title'));
    }

    public function test_shows_empty_state_on_load(): void
    {
        $this->get(route('tools.whois.index'))
            ->assertOk()
            ->assertSee(__('tools.whois.empty'));
    }

    public function test_empty_target_returns_error(): void
    {
        $this->post(route('tools.whois.lookup'), ['target' => ''])
            ->assertOk()
            ->assertSee(__('tools.whois.error_target_required'));
    }

    public function test_invalid_target_returns_error(): void
    {
        $this->post(route('tools.whois.lookup'), ['target' => 'not valid!!'])
            ->assertOk()
            ->assertSee(__('tools.whois.error_target_invalid'));
    }

    public function test_validate_target_accepts_ipv4(): void
    {
        $this->assertTrue(Whois::validateTarget('8.8.8.8'));
        $this->assertTrue(Whois::validateTarget('192.168.1.1'));
    }

    public function test_validate_target_accepts_ipv6(): void
    {
        $this->assertTrue(Whois::validateTarget('2001:db8::1'));
        $this->assertTrue(Whois::validateTarget('::1'));
    }

    public function test_validate_target_accepts_valid_domains(): void
    {
        $this->assertTrue(Whois::validateTarget('example.com'));
        $this->assertTrue(Whois::validateTarget('sub.example.co.uk'));
    }

    public function test_validate_target_rejects_invalid_input(): void
    {
        $this->assertFalse(Whois::validateTarget('not valid!!'));
        $this->assertFalse(Whois::validateTarget(''));
        $this->assertFalse(Whois::validateTarget('plainlabel'));
        $this->assertFalse(Whois::validateTarget(str_repeat('a', 254)));
    }

    public function test_lookup_returns_output_for_valid_target(): void
    {
        $result = (new Whois())->lookup('example.com');

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertIsArray($result['output']);
        $this->assertNotEmpty($result['output']);
    }
}
