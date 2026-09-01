<?php

namespace App\Tools\EmailHeaderAnalyzer;

class EmailHeaderAnalyzer
{
    private string $raw;

    private bool $performDnsLookups;

    public function __construct(string $raw, bool $performDnsLookups = true)
    {
        $this->raw              = $raw;
        $this->performDnsLookups = $performDnsLookups;
    }

    public function analyze(): array
    {
        if (blank($this->raw)) {
            return ['idle' => true];
        }

        $unfolded = $this->unfoldHeaders($this->raw);
        $headers  = $this->parseHeaders($unfolded);

        if (empty($headers)) {
            return ['idle' => false, 'valid' => false, 'error' => 'no_headers'];
        }

        $summary = $this->extractSummary($headers);
        $hops    = $this->extractHops($headers);
        $auth    = $this->extractAuth($headers);

        return [
            'idle'          => false,
            'valid'         => true,
            'summary'       => $summary,
            'hops'          => $hops,
            'total_seconds' => $this->totalSeconds($hops),
            'auth'          => $auth,
            'all_headers'   => $headers,
        ];
    }

    // ── Header parsing ────────────────────────────────────────────────────────

    private function unfoldHeaders(string $raw): string
    {
        // RFC 2822: continuation lines start with a TAB or SPACE
        return preg_replace('/\r?\n[ \t]+/', ' ', $raw) ?? $raw;
    }

    private function parseHeaders(string $unfolded): array
    {
        $headers = [];
        $lines   = preg_split('/\r?\n/', $unfolded) ?: [];

        foreach ($lines as $line) {
            if (blank($line)) {
                continue;
            }
            $colon = strpos($line, ':');
            if ($colon === false || $colon === 0) {
                continue;
            }
            $name  = trim(substr($line, 0, $colon));
            $value = trim(substr($line, $colon + 1));
            if (blank($name)) {
                continue;
            }
            $headers[] = ['name' => $name, 'value' => $value];
        }

        return $headers;
    }

    // ── Summary ──────────────────────────────────────────────────────────────

    private function extractSummary(array $headers): array
    {
        $map = [
            'from'       => ['from'],
            'to'         => ['to'],
            'subject'    => ['subject'],
            'date'       => ['date'],
            'message_id' => ['message-id'],
            'reply_to'   => ['reply-to'],
            'mailer'     => ['x-mailer', 'user-agent'],
        ];

        $summary = array_fill_keys(array_keys($map), null);

        foreach ($headers as $h) {
            $lower = strtolower($h['name']);
            foreach ($map as $key => $candidates) {
                if ($summary[$key] === null && in_array($lower, $candidates, true)) {
                    $summary[$key] = $h['value'];
                }
            }
        }

        return $summary;
    }

    // ── Hops (Received:) ─────────────────────────────────────────────────────

    private function extractHops(array $headers): array
    {
        $received = [];
        foreach ($headers as $h) {
            if (strtolower($h['name']) === 'received') {
                $received[] = $h['value'];
            }
        }

        // Reverse: bottom header = first hop (origin)
        $received = array_reverse($received);

        $hops    = [];
        $prevUnix = null;

        foreach ($received as $value) {
            $hop = $this->parseReceived($value);

            if ($prevUnix !== null && $hop['unix'] !== null) {
                $hop['delay_seconds'] = max(0, $hop['unix'] - $prevUnix);
            } else {
                $hop['delay_seconds'] = null;
            }

            if ($hop['unix'] !== null) {
                $prevUnix = $hop['unix'];
            }

            $hops[] = $hop;
        }

        return $hops;
    }

    private function parseReceived(string $value): array
    {
        // Truncate at the semicolon so date parsing doesn't interfere
        $semi    = strrpos($value, ';');
        $body    = $semi !== false ? substr($value, 0, $semi) : $value;
        $dateStr = $semi !== false ? trim(substr($value, $semi + 1)) : '';

        $from     = $this->extractReceivedHost($body, 'from');
        $by       = $this->extractReceivedHost($body, 'by');

        $unix      = null;
        $timestamp = '';
        if ($dateStr !== '') {
            $parsed = strtotime($dateStr);
            if ($parsed !== false) {
                $unix      = $parsed;
                $timestamp = date('Y-m-d H:i:s T', $parsed);
            } else {
                $timestamp = $dateStr;
            }
        }

        return [
            'from'          => $from,
            'by'            => $by,
            'timestamp'     => $timestamp,
            'unix'          => $unix,
            'delay_seconds' => null,
        ];
    }

    /**
     * Extract hostname, optional RDNS name and IP from a Received: clause.
     *
     * Typical formats:
     *   from hostname (rdns [1.2.3.4])
     *   from hostname ([1.2.3.4])
     *   from hostname (hostname [1.2.3.4]:port)
     *   by hostname (software)
     *   by hostname
     */
    private function extractReceivedHost(string $body, string $keyword): array
    {
        // Match the keyword and capture everything up to the next keyword or end
        $pattern = '/\b' . $keyword . '\s+(\S+)(?:\s+\(([^)]*)\))?/i';

        if (! preg_match($pattern, $body, $m)) {
            return ['host' => '', 'rdns' => null, 'ip' => null];
        }

        $host    = $m[1];
        $paren   = $m[2] ?? '';

        $rdns = null;
        $ip   = null;

        if ($paren !== '') {
            // Extract IP address (IPv4 or IPv6) inside square brackets
            if (preg_match('/\[([0-9a-fA-F.:]+)\]/', $paren, $im)) {
                $ip = $im[1];
            }

            // The part before the bracket (if present) is the RDNS name
            $beforeBracket = trim(preg_replace('/\[.*?\].*$/', '', $paren));
            if ($beforeBracket !== '' && $beforeBracket !== $host) {
                // Filter out software version strings (contain spaces or slashes only)
                if (str_contains($beforeBracket, '.') || str_contains($beforeBracket, ':')) {
                    $rdns = rtrim($beforeBracket, ' ,');
                }
            }
        }

        // If host itself looks like an IP, promote it
        if ($ip === null && filter_var($host, FILTER_VALIDATE_IP)) {
            $ip   = $host;
            $host = '';
        }

        return [
            'host' => $host,
            'rdns' => $rdns,
            'ip'   => $ip,
        ];
    }

    private function totalSeconds(array $hops): ?int
    {
        $timestamps = array_filter(array_column($hops, 'unix'));
        if (count($timestamps) < 2) {
            return null;
        }
        return max($timestamps) - min($timestamps);
    }

    // ── Authentication-Results ────────────────────────────────────────────────

    private function extractAuth(array $headers): array
    {
        $authValues = $this->collectAuthHeaders($headers);

        $spf   = ['result' => null, 'domain' => null, 'dns_queried' => null, 'dns_record' => null];
        $dkim  = ['result' => null, 'domain' => null, 'selector' => null,
                  'dns_name' => null, 'dns_record' => null, 'signatures' => []];
        $dmarc = ['result' => null, 'domain' => null, 'policy' => null,
                  'dns_name' => null, 'dns_record' => null];

        foreach ($authValues as $value) {
            $parsed = $this->parseSingleAuthHeader($value);

            if ($spf['result'] === null && $parsed['spf']['result'] !== null) {
                $spf['result'] = $parsed['spf']['result'];
            }
            if ($spf['domain'] === null && $parsed['spf']['domain'] !== null) {
                $spf['domain'] = $parsed['spf']['domain'];
            }

            if ($dkim['result'] === null && $parsed['dkim']['result'] !== null) {
                $dkim['result'] = $parsed['dkim']['result'];
            }
            if ($dkim['domain'] === null && $parsed['dkim']['domain'] !== null) {
                $dkim['domain'] = $parsed['dkim']['domain'];
            }
            if ($dkim['selector'] === null && $parsed['dkim']['selector'] !== null) {
                $dkim['selector'] = $parsed['dkim']['selector'];
            }

            if ($dmarc['result'] === null && $parsed['dmarc']['result'] !== null) {
                $dmarc['result'] = $parsed['dmarc']['result'];
            }
            if ($dmarc['domain'] === null && $parsed['dmarc']['domain'] !== null) {
                $dmarc['domain'] = $parsed['dmarc']['domain'];
            }
            if ($dmarc['policy'] === null && $parsed['dmarc']['policy'] !== null) {
                $dmarc['policy'] = $parsed['dmarc']['policy'];
            }
        }

        // Fallback: DKIM-Signature headers when Authentication-Results incomplete
        $signatures       = $this->parseDkimSignatures($headers);
        $dkim['signatures'] = $signatures;
        if (! empty($signatures)) {
            if ($dkim['domain'] === null) {
                $dkim['domain'] = $signatures[0]['domain'];
            }
            if ($dkim['selector'] === null) {
                $dkim['selector'] = $signatures[0]['selector'];
            }
        }

        // Fallback: From: header domain for SPF and DMARC
        foreach ($headers as $h) {
            if (strtolower($h['name']) === 'from') {
                if (preg_match('/@([\w.\-]+)/u', $h['value'], $m)) {
                    $fromDomain = strtolower($m[1]);
                    if ($spf['domain'] === null) {
                        $spf['domain'] = $fromDomain;
                    }
                    if ($dmarc['domain'] === null) {
                        $dmarc['domain'] = $fromDomain;
                    }
                }
                break;
            }
        }

        if ($this->performDnsLookups) {
            if ($spf['domain'] !== null) {
                $spf['dns_queried'] = $spf['domain'];
                $spf['dns_record']  = $this->lookupSpfDns($spf['domain']);
            }
            if ($dkim['selector'] !== null && $dkim['domain'] !== null) {
                $dkimDns            = $this->lookupDkimDns($dkim['selector'], $dkim['domain']);
                $dkim['dns_name']   = $dkimDns['name'];
                $dkim['dns_record'] = $dkimDns['record'];
            }
            if ($dmarc['domain'] !== null) {
                $dmarcDns            = $this->lookupDmarcDns($dmarc['domain']);
                $dmarc['dns_name']   = $dmarcDns['name'];
                $dmarc['dns_record'] = $dmarcDns['record'];
            }
        }

        $raw = empty($authValues) ? null : implode("\n\n", $authValues);

        return compact('spf', 'dkim', 'dmarc', 'raw');
    }

    private function collectAuthHeaders(array $headers): array
    {
        $values = [];
        foreach ($headers as $h) {
            if (strtolower($h['name']) === 'authentication-results') {
                $values[] = $h['value'];
            }
        }
        return $values;
    }

    private function parseSingleAuthHeader(string $value): array
    {
        $strip = static fn (string $s): string => strtolower(rtrim($s, ';,'));

        $spfResult  = null;
        $spfDomain  = null;
        if (preg_match('/\bspf=(\S+)/i', $value, $m)) {
            $spfResult = $strip($m[1]);
        }
        if (preg_match('/smtp\.(?:mailfrom|helo)=([^\s;]+)/i', $value, $m)) {
            $spfDomain = $this->extractDomainFromEmail($strip($m[1]));
        }

        $dkimResult   = null;
        $dkimDomain   = null;
        $dkimSelector = null;
        if (preg_match('/\bdkim=(\S+)/i', $value, $m)) {
            $dkimResult = $strip($m[1]);
        }
        if (preg_match('/header\.d=([^\s;]+)/i', $value, $m)) {
            $dkimDomain = $strip($m[1]);
        }
        if (preg_match('/header\.s=([^\s;]+)/i', $value, $m)) {
            $dkimSelector = $strip($m[1]);
        }

        $dmarcResult = null;
        $dmarcDomain = null;
        $dmarcPolicy = null;
        if (preg_match('/\bdmarc=(\S+)/i', $value, $m)) {
            $dmarcResult = $strip($m[1]);
        }
        if (preg_match('/header\.from=([^\s;]+)/i', $value, $m)) {
            $dmarcDomain = $this->extractDomainFromEmail($strip($m[1]));
        }
        if (preg_match('/\bp=([^\s;)]+)/i', $value, $m)) {
            $dmarcPolicy = $strip($m[1]);
        }

        return [
            'spf'   => ['result' => $spfResult,   'domain' => $spfDomain],
            'dkim'  => ['result' => $dkimResult,  'domain' => $dkimDomain,  'selector' => $dkimSelector],
            'dmarc' => ['result' => $dmarcResult, 'domain' => $dmarcDomain, 'policy'   => $dmarcPolicy],
        ];
    }

    private function parseDkimSignatures(array $headers): array
    {
        $signatures = [];
        foreach ($headers as $h) {
            if (strtolower($h['name']) !== 'dkim-signature') {
                continue;
            }
            $domain   = null;
            $selector = null;
            if (preg_match('/\bd=([^\s;]+)/i', $h['value'], $m)) {
                $domain = strtolower(rtrim($m[1], ';,'));
            }
            if (preg_match('/\bs=([^\s;]+)/i', $h['value'], $m)) {
                $selector = strtolower(rtrim($m[1], ';,'));
            }
            if ($domain !== null || $selector !== null) {
                $signatures[] = ['domain' => $domain, 'selector' => $selector];
            }
        }
        return $signatures;
    }

    private function lookupSpfDns(string $domain): ?string
    {
        try {
            $records = @dns_get_record($domain, DNS_TXT);
            if (is_array($records)) {
                foreach ($records as $r) {
                    $txt = $r['txt'] ?? ($r['entries'][0] ?? '');
                    if (str_starts_with(strtolower($txt), 'v=spf1')) {
                        return $txt;
                    }
                }
            }
        } catch (\Throwable) {}
        return null;
    }

    private function lookupDkimDns(string $selector, string $domain): array
    {
        $name   = $selector . '._domainkey.' . $domain;
        $record = null;
        try {
            $records = @dns_get_record($name, DNS_TXT);
            if (is_array($records)) {
                foreach ($records as $r) {
                    $txt = $r['txt'] ?? ($r['entries'][0] ?? '');
                    if ($txt !== '') {
                        $record = $txt;
                        break;
                    }
                }
            }
        } catch (\Throwable) {}
        return ['name' => $name, 'record' => $record];
    }

    private function lookupDmarcDns(string $domain): array
    {
        $name   = '_dmarc.' . $domain;
        $record = null;
        try {
            $records = @dns_get_record($name, DNS_TXT);
            if (is_array($records)) {
                foreach ($records as $r) {
                    $txt = $r['txt'] ?? ($r['entries'][0] ?? '');
                    if (str_starts_with(strtolower($txt), 'v=dmarc1')) {
                        $record = $txt;
                        break;
                    }
                }
            }
        } catch (\Throwable) {}
        return ['name' => $name, 'record' => $record];
    }

    private function extractDomainFromEmail(string $emailOrDomain): string
    {
        if (str_contains($emailOrDomain, '@')) {
            return strtolower(substr($emailOrDomain, strpos($emailOrDomain, '@') + 1));
        }
        return strtolower($emailOrDomain);
    }

    // ── Formatting helper ─────────────────────────────────────────────────────

    public static function formatDelay(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' s';
        }
        $minutes = intdiv($seconds, 60);
        $secs    = $seconds % 60;
        if ($minutes < 60) {
            return $secs > 0 ? "{$minutes} m {$secs} s" : "{$minutes} m";
        }
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;
        return $mins > 0 ? "{$hours} h {$mins} m" : "{$hours} h";
    }
}
