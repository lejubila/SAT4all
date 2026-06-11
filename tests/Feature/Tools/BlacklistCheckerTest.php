<?php

namespace Tests\Feature\Tools;

use Tests\TestCase;

class BlacklistCheckerTest extends TestCase
{
    // ── Page ─────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.blacklist-checker.index'))
            ->assertOk()
            ->assertSee(__('tools.blacklist_checker.title'));
    }

    // ── Validation ────────────────────────────────────────────────────────────

    public function test_empty_target_returns_error(): void
    {
        $this->post(route('tools.blacklist-checker.check'), ['target' => ''])
            ->assertOk()
            ->assertSee(__('tools.blacklist_checker.error_target_required'));
    }

    public function test_invalid_target_returns_error(): void
    {
        $this->post(route('tools.blacklist-checker.check'), ['target' => 'not valid!!!'])
            ->assertOk()
            ->assertSee(__('tools.blacklist_checker.error_target_invalid'));
    }

    public function test_too_long_target_returns_error(): void
    {
        $this->post(route('tools.blacklist-checker.check'), [
            'target' => str_repeat('a', 254) . '.com',
        ])->assertOk()
          ->assertSee(__('tools.blacklist_checker.error_target_too_long'));
    }

    // ── IPv4 input ────────────────────────────────────────────────────────────

    public function test_valid_ipv4_returns_result(): void
    {
        $response = $this->post(route('tools.blacklist-checker.check'), [
            'target' => '8.8.8.8',
        ]);

        $response->assertOk()
                 ->assertSee('8.8.8.8')
                 ->assertSee(__('tools.blacklist_checker.col_name'))
                 ->assertSee(__('tools.blacklist_checker.col_status'));
    }

    public function test_ipv4_result_has_correct_total_checked(): void
    {
        // IPv4 input → only IP RBLs checked (9 zones)
        $response = $this->post(route('tools.blacklist-checker.check'), [
            'target' => '8.8.8.8',
        ]);

        $response->assertOk()
                 ->assertSee('/ 9');
    }

    // ── Domain input ──────────────────────────────────────────────────────────

    public function test_valid_domain_returns_result(): void
    {
        $response = $this->post(route('tools.blacklist-checker.check'), [
            'target' => 'gmail.com',
        ]);

        $response->assertOk()
                 ->assertSee(__('tools.blacklist_checker.col_name'));
    }

    public function test_email_address_is_normalised(): void
    {
        // user@gmail.com → extract gmail.com
        $response = $this->post(route('tools.blacklist-checker.check'), [
            'target' => 'user@gmail.com',
        ]);

        $response->assertOk()
                 ->assertSee('gmail.com');
    }

    // ── Summary states ────────────────────────────────────────────────────────

    public function test_result_shows_summary_section(): void
    {
        $response = $this->post(route('tools.blacklist-checker.check'), [
            'target' => '8.8.8.8',
        ]);

        $response->assertOk();
        // Either summary message must be present (don't assume blacklist state)
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, __('tools.blacklist_checker.summary_clean'))
            || str_contains($content, __('tools.blacklist_checker.summary_listed'))
        );
    }
}
