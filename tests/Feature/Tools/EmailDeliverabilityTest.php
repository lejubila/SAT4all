<?php

namespace Tests\Feature\Tools;

use Tests\TestCase;

class EmailDeliverabilityTest extends TestCase
{
    // ── Page ─────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.email-deliverability.index'))
            ->assertOk()
            ->assertSee(__('tools.email_deliverability.title'));
    }

    // ── Validation ────────────────────────────────────────────────────────────

    public function test_empty_domain_returns_error(): void
    {
        $this->post(route('tools.email-deliverability.check'), ['domain' => ''])
            ->assertOk()
            ->assertSee(__('tools.email_deliverability.error_domain_required'));
    }

    public function test_invalid_domain_returns_error(): void
    {
        $this->post(route('tools.email-deliverability.check'), ['domain' => 'not a domain!!!'])
            ->assertOk()
            ->assertSee(__('tools.email_deliverability.error_domain_invalid'));
    }

    public function test_too_long_domain_returns_error(): void
    {
        $this->post(route('tools.email-deliverability.check'), [
            'domain' => str_repeat('a', 254) . '.com',
        ])->assertOk()
          ->assertSee(__('tools.email_deliverability.error_domain_too_long'));
    }

    // ── Domain normalisation ──────────────────────────────────────────────────

    public function test_email_address_input_is_normalised(): void
    {
        // An email address should extract the domain part and process it
        $response = $this->post(route('tools.email-deliverability.check'), [
            'domain' => 'user@gmail.com',
        ]);

        $response->assertOk()
                 ->assertSee('gmail.com');
    }

    // ── Live DNS queries (using well-known domains) ───────────────────────────

    public function test_valid_domain_returns_result_sections(): void
    {
        $response = $this->post(route('tools.email-deliverability.check'), [
            'domain' => 'gmail.com',
        ]);

        $response->assertOk()
                 ->assertSee('gmail.com')
                 ->assertSee('SPF')
                 ->assertSee('DMARC')
                 ->assertSee('DKIM')
                 ->assertSee('MX');
    }

    public function test_gmail_has_mx_records(): void
    {
        $response = $this->post(route('tools.email-deliverability.check'), [
            'domain' => 'gmail.com',
        ]);

        $response->assertOk()
                 ->assertSee(__('tools.email_deliverability.found'));
    }

    // ── Non-existent domain ───────────────────────────────────────────────────

    public function test_nonexistent_domain_shows_not_found(): void
    {
        $response = $this->post(route('tools.email-deliverability.check'), [
            'domain' => 'thisdoesnotexist99999xyz.invalid',
        ]);

        $response->assertOk()
                 ->assertSee(__('tools.email_deliverability.not_found'));
    }

    // ── DKIM selector ─────────────────────────────────────────────────────────

    public function test_dkim_selector_appears_in_result(): void
    {
        $response = $this->post(route('tools.email-deliverability.check'), [
            'domain'        => 'gmail.com',
            'dkim_selector' => 'google',
        ]);

        $response->assertOk()
                 ->assertSee('google');
    }
}
