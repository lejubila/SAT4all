<?php

namespace App\Tools\BlacklistChecker;

class BlacklistChecker
{
    private const IP_RBLS = [
        'zen.spamhaus.org'       => 'Spamhaus ZEN (SBL+XBL+PBL)',
        'bl.spamcop.net'         => 'SpamCop',
        'dnsbl.sorbs.net'        => 'SORBS composite',
        'spam.dnsbl.sorbs.net'   => 'SORBS spam',
        'b.barracudacentral.org' => 'Barracuda BRBL',
        'dnsbl-1.uceprotect.net' => 'UCEPROTECT Level 1',
        'cbl.abuseat.org'        => 'CBL',
        'psbl.surriel.com'       => 'PSBL',
        'ix.dnsbl.manitu.net'    => 'Heise iX',
    ];

    private const DOMAIN_RBLS = [
        'dbl.spamhaus.org' => 'Spamhaus DBL',
        'multi.surbl.org'  => 'SURBL composite',
    ];

    private string $rawInput;
    private ?string $ip     = null;
    private ?string $domain = null;
    private bool $ipResolved = false;

    public function __construct(string $input)
    {
        $this->rawInput = $input;
        $this->normalizeInput($input);
    }

    public function check(): array
    {
        $results = [];

        // IP RBLs — only if we have an IPv4
        if ($this->ip !== null) {
            $reversed = $this->reverseIp($this->ip);
            foreach (self::IP_RBLS as $zone => $name) {
                $rbl = $this->checkRbl($reversed . '.' . $zone);
                $results[] = array_merge($rbl, ['zone' => $zone, 'name' => $name, 'type' => 'ip']);
            }
        }

        // Domain RBLs — only if we have a domain
        if ($this->domain !== null) {
            foreach (self::DOMAIN_RBLS as $zone => $name) {
                $rbl = $this->checkRbl($this->domain . '.' . $zone);
                $results[] = array_merge($rbl, ['zone' => $zone, 'name' => $name, 'type' => 'domain']);
            }
        }

        $listedCount = count(array_filter($results, fn ($r) => $r['listed']));

        return [
            'input'         => $this->rawInput,
            'ip'            => $this->ip,
            'domain'        => $this->domain,
            'ip_resolved'   => $this->ipResolved,
            'listed_count'  => $listedCount,
            'total_checked' => count($results),
            'results'       => $results,
        ];
    }

    // ── Input handling ────────────────────────────────────────────────────────

    private function normalizeInput(string $raw): void
    {
        $raw = trim($raw);

        // Extract domain from email address
        if (str_contains($raw, '@')) {
            $raw = substr($raw, strpos($raw, '@') + 1);
        }

        // Strip protocol / path
        $raw = preg_replace('~^https?://~i', '', $raw) ?? $raw;
        $raw = explode('/', $raw)[0];
        $raw = strtolower(rtrim($raw, '.'));

        if (filter_var($raw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->ip = $raw;
            return;
        }

        // It's a domain — store it and try to resolve to IPv4
        $this->domain = $raw;
        $records = @dns_get_record($raw, DNS_A);
        if (! empty($records)) {
            $this->ip          = $records[0]['ip'];
            $this->ipResolved  = true;
        }
    }

    // ── RBL check ─────────────────────────────────────────────────────────────

    private function reverseIp(string $ip): string
    {
        return implode('.', array_reverse(explode('.', $ip)));
    }

    private function checkRbl(string $query): array
    {
        $aRecords   = @dns_get_record($query, DNS_A);
        $listed     = ! empty($aRecords);
        $result     = $listed ? ($aRecords[0]['ip'] ?? null) : null;
        $reason     = null;

        if ($listed) {
            $txtRecords = @dns_get_record($query, DNS_TXT);
            if (! empty($txtRecords)) {
                $reason = $txtRecords[0]['txt'] ?? $txtRecords[0]['entries'][0] ?? null;
            }
        }

        return compact('listed', 'result', 'reason');
    }
}
