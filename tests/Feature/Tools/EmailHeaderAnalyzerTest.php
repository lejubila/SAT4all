<?php

namespace Tests\Feature\Tools;

use Tests\TestCase;

class EmailHeaderAnalyzerTest extends TestCase
{
    private const HEADER_SIMPLE = <<<'EOH'
        From: Alice <alice@example.com>
        To: Bob <bob@example.com>
        Subject: Test email
        Date: Mon, 10 Jun 2026 12:00:00 +0000
        Message-ID: <abc123@example.com>
        EOH;

    private const HEADER_WITH_RECEIVED = <<<'EOH'
        Received: from mail.origin.com (mail.origin.com [203.0.113.10])
                by mx.destination.com (Postfix) with ESMTP; Mon, 10 Jun 2026 12:00:30 +0000
        Received: from [192.168.1.5] (unknown [192.168.1.5])
                by mail.origin.com (Postfix) with ESMTP; Mon, 10 Jun 2026 12:00:00 +0000
        From: Alice <alice@example.com>
        To: Bob <bob@example.com>
        Subject: Hop test
        Date: Mon, 10 Jun 2026 12:00:30 +0000
        EOH;

    private const HEADER_WITH_AUTH = <<<'EOH'
        Authentication-Results: mx.example.com;
            spf=pass smtp.mailfrom=example.com;
            dkim=pass header.d=example.com;
            dmarc=pass action=none header.from=example.com
        From: alice@example.com
        Subject: Auth test
        EOH;

    // ── Page ─────────────────────────────────────────────────────────────────

    public function test_shows_page(): void
    {
        $this->get(route('tools.email-header-analyzer.index'))
            ->assertOk()
            ->assertSee(__('tools.email_header_analyzer.title'));
    }

    // ── Empty input ──────────────────────────────────────────────────────────

    public function test_empty_input_returns_idle(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), ['header' => ''])
            ->assertOk()
            ->assertSee(__('tools.email_header_analyzer.empty'));
    }

    // ── Summary ───────────────────────────────────────────────────────────────

    public function test_minimal_header_extracts_summary(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => self::HEADER_SIMPLE,
        ])->assertOk()
          ->assertSee('alice@example.com')
          ->assertSee('Test email');
    }

    public function test_subject_visible_in_result(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => self::HEADER_SIMPLE,
        ])->assertOk()
          ->assertSee('Test email');
    }

    // ── Delivery trace ───────────────────────────────────────────────────────

    public function test_received_headers_produce_hops(): void
    {
        $response = $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => self::HEADER_WITH_RECEIVED,
        ])->assertOk();

        // Two Received: headers → two rows in trace table
        $response->assertSee('mail.origin.com');
        $response->assertSee('mx.destination.com');
    }

    public function test_delay_is_calculated_between_hops(): void
    {
        // Two Received: headers 30 seconds apart → delay badge should show 30 s
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => self::HEADER_WITH_RECEIVED,
        ])->assertOk()
          ->assertSee('30 s');
    }

    // ── Authentication ───────────────────────────────────────────────────────

    public function test_authentication_results_extracted(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => self::HEADER_WITH_AUTH,
        ])->assertOk()
          ->assertSee('PASS');
    }

    // ── Validation ───────────────────────────────────────────────────────────

    public function test_too_large_input_returns_error(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => str_repeat('From: x@x.com' . "\n", 4000),
        ])->assertOk()
          ->assertSee(__('tools.email_header_analyzer.error_too_large'));
    }

    // ── Malformed input ───────────────────────────────────────────────────────

    public function test_header_without_colon_does_not_crash(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => "no colons here\njust plain text\n",
        ])->assertOk()
          ->assertSee(__('tools.email_header_analyzer.error_no_headers'));
    }

    // ── Section headings always visible ──────────────────────────────────────

    public function test_section_headings_visible_in_valid_result(): void
    {
        $this->post(route('tools.email-header-analyzer.analyze'), [
            'header' => self::HEADER_SIMPLE,
        ])->assertOk()
          ->assertSee(__('tools.email_header_analyzer.section_summary'))
          ->assertSee(__('tools.email_header_analyzer.section_auth'))
          ->assertSee(__('tools.email_header_analyzer.section_all_headers'));
    }
}
