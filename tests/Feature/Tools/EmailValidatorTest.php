<?php

namespace Tests\Feature\Tools;

use Tests\TestCase;

class EmailValidatorTest extends TestCase
{
    // ── Page ─────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.email-validator.index'))
            ->assertOk()
            ->assertSee(__('tools.email_validator.title'));
    }

    private function postWithCaptcha(array $data): \Illuminate\Testing\TestResponse
    {
        return $this->withSession(['email_validator_captcha' => 'TESTAB'])
                    ->post(route('tools.email-validator.check'), array_merge($data, ['captcha_input' => 'TESTAB']));
    }

    // ── Validation ────────────────────────────────────────────────────────────

    public function test_empty_email_returns_error(): void
    {
        $this->postWithCaptcha(['email' => ''])
            ->assertOk()
            ->assertSee(__('tools.email_validator.error_email_required'));
    }

    public function test_wrong_captcha_returns_error(): void
    {
        $this->withSession(['email_validator_captcha' => 'TESTAB'])
             ->post(route('tools.email-validator.check'), ['email' => 'user@example.com', 'captcha_input' => 'WRONG1'])
             ->assertOk()
             ->assertSee(__('tools.email_validator.error_captcha'));
    }

    public function test_missing_captcha_returns_error(): void
    {
        $this->post(route('tools.email-validator.check'), ['email' => 'user@example.com'])
             ->assertOk()
             ->assertSee(__('tools.email_validator.error_captcha_required'));
    }

    // ── Syntax checks ─────────────────────────────────────────────────────────

    public function test_invalid_syntax_shows_invalid_result(): void
    {
        $response = $this->postWithCaptcha(['email' => 'not-an-email']);
        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, __('tools.email_validator.syntax_invalid'))
            || str_contains($content, __('tools.email_validator.overall_invalid'))
        );
    }

    public function test_valid_syntax_shows_local_and_domain_parts(): void
    {
        $response = $this->postWithCaptcha(['email' => 'user@gmail.com']);
        $response->assertOk()
                 ->assertSee('user')
                 ->assertSee('gmail.com');
    }

    // ── Live DNS ──────────────────────────────────────────────────────────────

    public function test_domain_without_mx_shows_invalid(): void
    {
        $response = $this->postWithCaptcha(['email' => 'test@thisdoesnotexist99999xyz.invalid']);
        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, __('tools.email_validator.mx_not_found'))
            || str_contains($content, __('tools.email_validator.overall_invalid'))
        );
    }

    public function test_valid_domain_shows_mx_records(): void
    {
        $this->postWithCaptcha(['email' => 'test@gmail.com'])
             ->assertOk()
             ->assertSee(__('tools.email_validator.section_mx'));
    }

    public function test_result_shows_smtp_section_for_valid_domain(): void
    {
        $response = $this->postWithCaptcha(['email' => 'test@gmail.com']);
        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, __('tools.email_validator.section_smtp'))
        );
    }

    public function test_overall_result_is_shown(): void
    {
        $response = $this->postWithCaptcha(['email' => 'test@gmail.com']);
        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, __('tools.email_validator.overall_valid'))
            || str_contains($content, __('tools.email_validator.overall_invalid'))
            || str_contains($content, __('tools.email_validator.overall_unknown'))
            || str_contains($content, __('tools.email_validator.overall_risky'))
        );
    }
}
