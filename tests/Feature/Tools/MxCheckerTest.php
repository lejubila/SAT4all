<?php

namespace Tests\Feature\Tools;

use Tests\TestCase;

class MxCheckerTest extends TestCase
{
    // ── Page ─────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.mx-checker.index'))
            ->assertOk()
            ->assertSee(__('tools.mx_checker.title'));
    }

    private function postWithCaptcha(array $data): \Illuminate\Testing\TestResponse
    {
        return $this->withSession(['mx_checker_captcha' => 'TESTAB'])
                    ->post(route('tools.mx-checker.check'), array_merge($data, ['captcha_input' => 'TESTAB']));
    }

    // ── Validation ────────────────────────────────────────────────────────────

    public function test_empty_domain_returns_error(): void
    {
        $this->postWithCaptcha(['domain' => ''])
            ->assertOk()
            ->assertSee(__('tools.mx_checker.error_domain_required'));
    }

    public function test_invalid_domain_returns_error(): void
    {
        $this->postWithCaptcha(['domain' => 'not valid!!!'])
            ->assertOk()
            ->assertSee(__('tools.mx_checker.error_domain_invalid'));
    }

    public function test_too_long_domain_returns_error(): void
    {
        $this->postWithCaptcha(['domain' => str_repeat('a', 254) . '.com'])
            ->assertOk()
            ->assertSee(__('tools.mx_checker.error_domain_too_long'));
    }

    public function test_wrong_captcha_returns_error(): void
    {
        $this->withSession(['mx_checker_captcha' => 'TESTAB'])
             ->post(route('tools.mx-checker.check'), ['domain' => 'gmail.com', 'captcha_input' => 'WRONG1'])
             ->assertOk()
             ->assertSee(__('tools.mx_checker.error_captcha'));
    }

    public function test_missing_captcha_returns_error(): void
    {
        $this->post(route('tools.mx-checker.check'), ['domain' => 'gmail.com'])
             ->assertOk()
             ->assertSee(__('tools.mx_checker.error_captcha_required'));
    }

    // ── Domain normalisation ──────────────────────────────────────────────────

    public function test_email_address_is_normalised(): void
    {
        $this->postWithCaptcha(['domain' => 'user@gmail.com'])
             ->assertOk()
             ->assertSee('gmail.com');
    }

    // ── Live DNS / SMTP ───────────────────────────────────────────────────────

    public function test_valid_domain_returns_mx_results(): void
    {
        $this->postWithCaptcha(['domain' => 'gmail.com'])
             ->assertOk()
             ->assertSee('gmail.com')
             ->assertSee('gmail-smtp-in.l.google.com');
    }

    public function test_domain_without_mx_shows_no_mx_message(): void
    {
        $this->postWithCaptcha(['domain' => 'thisdoesnotexist99999xyz.invalid'])
             ->assertOk()
             ->assertSee(__('tools.mx_checker.no_mx'));
    }

    public function test_result_shows_priority_label(): void
    {
        $this->postWithCaptcha(['domain' => 'gmail.com'])
             ->assertOk()
             ->assertSee(__('tools.mx_checker.priority'));
    }

    public function test_result_shows_reachability_badge(): void
    {
        $response = $this->postWithCaptcha(['domain' => 'gmail.com']);
        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, __('tools.mx_checker.reachable'))
            || str_contains($content, __('tools.mx_checker.unreachable'))
        );
    }
}
