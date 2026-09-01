<?php

namespace Tests\Unit\Tools;

use App\Tools\EmailHeaderAnalyzer\EmailHeaderAnalyzer;
use PHPUnit\Framework\TestCase;

class EmailHeaderAnalyzerUnitTest extends TestCase
{
    private function analyze(string $header): array
    {
        return (new EmailHeaderAnalyzer($header, performDnsLookups: false))->analyze();
    }

    public function test_auth_result_structure_has_all_keys(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx.example.com;
                spf=pass smtp.mailfrom=example.com;
                dkim=pass header.d=example.com header.s=sel1;
                dmarc=pass (p=none) header.from=example.com
            From: alice@example.com
            EOH;

        $auth = $this->analyze($header)['auth'];

        $this->assertArrayHasKey('result',      $auth['spf']);
        $this->assertArrayHasKey('domain',      $auth['spf']);
        $this->assertArrayHasKey('dns_queried', $auth['spf']);
        $this->assertArrayHasKey('dns_record',  $auth['spf']);

        $this->assertArrayHasKey('result',     $auth['dkim']);
        $this->assertArrayHasKey('domain',     $auth['dkim']);
        $this->assertArrayHasKey('selector',   $auth['dkim']);
        $this->assertArrayHasKey('dns_name',   $auth['dkim']);
        $this->assertArrayHasKey('dns_record', $auth['dkim']);
        $this->assertArrayHasKey('signatures', $auth['dkim']);

        $this->assertArrayHasKey('result',     $auth['dmarc']);
        $this->assertArrayHasKey('domain',     $auth['dmarc']);
        $this->assertArrayHasKey('policy',     $auth['dmarc']);
        $this->assertArrayHasKey('dns_name',   $auth['dmarc']);
        $this->assertArrayHasKey('dns_record', $auth['dmarc']);
    }

    public function test_spf_domain_extracted_from_email_in_mailfrom(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx.example.com;
                spf=softfail smtp.mailfrom=attacker@evil.com
            From: user@example.com
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertSame('softfail', $auth['spf']['result']);
        $this->assertSame('evil.com', $auth['spf']['domain']);
    }

    public function test_dkim_selector_extracted_from_auth_results(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx.example.com;
                dkim=pass header.d=sender.com header.s=key2048
            From: user@sender.com
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertSame('pass',       $auth['dkim']['result']);
        $this->assertSame('sender.com', $auth['dkim']['domain']);
        $this->assertSame('key2048',    $auth['dkim']['selector']);
    }

    public function test_dkim_selector_falls_back_to_dkim_signature_header(): void
    {
        $header = <<<'EOH'
            DKIM-Signature: v=1; a=rsa-sha256; d=fallback.com; s=s1024; b=abc==
            From: user@fallback.com
            Subject: Test
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertSame('fallback.com', $auth['dkim']['domain']);
        $this->assertSame('s1024',        $auth['dkim']['selector']);
        $this->assertCount(1, $auth['dkim']['signatures']);
    }

    public function test_dmarc_policy_extracted_from_parenthetical(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx.example.com;
                dmarc=pass (p=quarantine dis=none) header.from=example.com
            From: user@example.com
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertSame('quarantine',  $auth['dmarc']['policy']);
        $this->assertSame('example.com', $auth['dmarc']['domain']);
    }

    public function test_multiple_auth_headers_merged_first_non_null_wins(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx2.example.com;
                dkim=pass header.d=example.com header.s=google
            Authentication-Results: mx1.example.com;
                spf=fail smtp.mailfrom=spammer@evil.com
            From: user@example.com
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertSame('pass',     $auth['dkim']['result']);
        $this->assertSame('google',   $auth['dkim']['selector']);
        $this->assertSame('fail',     $auth['spf']['result']);
        $this->assertSame('evil.com', $auth['spf']['domain']);
    }

    public function test_spf_domain_falls_back_to_from_header(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx.example.com; spf=neutral
            From: sender@fallback-domain.com
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertSame('neutral',             $auth['spf']['result']);
        $this->assertSame('fallback-domain.com', $auth['spf']['domain']);
    }

    public function test_raw_concatenates_all_auth_headers(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx2.example.com; dkim=pass
            Authentication-Results: mx1.example.com; spf=pass
            From: user@example.com
            EOH;

        $auth = $this->analyze($header)['auth'];
        $this->assertStringContainsString('mx2.example.com', $auth['raw']);
        $this->assertStringContainsString('mx1.example.com', $auth['raw']);
    }

    public function test_dns_fields_null_when_lookups_disabled(): void
    {
        $header = <<<'EOH'
            Authentication-Results: mx.example.com;
                spf=pass smtp.mailfrom=example.com;
                dkim=pass header.d=example.com header.s=sel;
                dmarc=pass header.from=example.com
            From: user@example.com
            EOH;

        $auth = $this->analyze($header)['auth'];

        $this->assertNull($auth['spf']['dns_record']);
        $this->assertNull($auth['spf']['dns_queried']);
        $this->assertNull($auth['dkim']['dns_record']);
        $this->assertNull($auth['dkim']['dns_name']);
        $this->assertNull($auth['dmarc']['dns_record']);
        $this->assertNull($auth['dmarc']['dns_name']);
    }
}
