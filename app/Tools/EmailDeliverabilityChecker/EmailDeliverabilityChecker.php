<?php

namespace App\Tools\EmailDeliverabilityChecker;

class EmailDeliverabilityChecker
{
    private string $domain;
    private ?string $dkimSelector;

    private const COMMON_SELECTORS = [
        'google', 'default', 'k1', 'mail',
        'selector1', 'selector2', 'smtp', 'dkim',
    ];

    public function __construct(string $domain, ?string $dkimSelector = null)
    {
        $this->domain       = $this->normalizeDomain($domain);
        $this->dkimSelector = $dkimSelector !== null ? trim($dkimSelector) : null;
    }

    public function check(): array
    {
        return [
            'domain' => $this->domain,
            'mx'     => $this->queryMx(),
            'spf'    => $this->querySpf(),
            'dmarc'  => $this->queryDmarc(),
            'dkim'   => $this->resolveDkim(),
        ];
    }

    // ── Normalisation ─────────────────────────────────────────────────────────

    private function normalizeDomain(string $input): string
    {
        $input = trim($input);

        // Extract domain from email address
        if (str_contains($input, '@')) {
            $input = substr($input, strpos($input, '@') + 1);
        }

        // Strip protocol
        $input = preg_replace('~^https?://~i', '', $input) ?? $input;

        // Strip path/query
        $input = explode('/', $input)[0];
        $input = explode('?', $input)[0];

        return strtolower(rtrim($input, '.'));
    }

    // ── MX ────────────────────────────────────────────────────────────────────

    private function queryMx(): array
    {
        $records = @dns_get_record($this->domain, DNS_MX);
        if (empty($records)) {
            return [];
        }

        $mx = array_map(fn ($r) => [
            'priority' => (int) ($r['pri'] ?? 0),
            'host'     => rtrim($r['target'] ?? '', '.'),
        ], $records);

        usort($mx, fn ($a, $b) => $a['priority'] <=> $b['priority']);

        return $mx;
    }

    // ── SPF ───────────────────────────────────────────────────────────────────

    private function querySpf(): array
    {
        $records = @dns_get_record($this->domain, DNS_TXT);
        $spfRaw  = null;

        if (is_array($records)) {
            foreach ($records as $r) {
                $txt = $r['txt'] ?? $r['entries'][0] ?? '';
                if (str_starts_with(strtolower($txt), 'v=spf1')) {
                    $spfRaw = $txt;
                    break;
                }
            }
        }

        if ($spfRaw === null) {
            return ['found' => false, 'record' => null, 'all' => null];
        }

        $all = null;
        if (preg_match('/([~\-\+?])all\b/i', $spfRaw, $m)) {
            $all = strtolower($m[0]);
        }

        return ['found' => true, 'record' => $spfRaw, 'all' => $all];
    }

    // ── DMARC ─────────────────────────────────────────────────────────────────

    private function queryDmarc(): array
    {
        $records = @dns_get_record('_dmarc.' . $this->domain, DNS_TXT);
        $raw     = null;

        if (is_array($records)) {
            foreach ($records as $r) {
                $txt = $r['txt'] ?? $r['entries'][0] ?? '';
                if (str_starts_with(strtolower($txt), 'v=dmarc1')) {
                    $raw = $txt;
                    break;
                }
            }
        }

        if ($raw === null) {
            return ['found' => false, 'record' => null, 'policy' => null, 'sp' => null, 'pct' => null, 'rua' => null];
        }

        $get = fn (string $tag) => preg_match('/\b' . $tag . '=([^;]+)/i', $raw, $m) ? trim($m[1]) : null;

        $policy = $get('p');
        $sp     = $get('sp');
        $rua    = $get('rua');
        $pct    = $get('pct') !== null ? (int) $get('pct') : null;

        return compact('raw', 'policy', 'sp', 'pct', 'rua') + ['found' => true, 'record' => $raw];
    }

    // ── DKIM ──────────────────────────────────────────────────────────────────

    private function resolveDkim(): array
    {
        if (filled($this->dkimSelector)) {
            return $this->queryDkim($this->dkimSelector);
        }

        return $this->tryCommonDkimSelectors();
    }

    private function queryDkim(string $selector): array
    {
        $host    = $selector . '._domainkey.' . $this->domain;
        $records = @dns_get_record($host, DNS_TXT);
        $raw     = null;

        if (is_array($records)) {
            foreach ($records as $r) {
                $txt = $r['txt'] ?? $r['entries'][0] ?? '';
                if (filled($txt)) {
                    $raw = $txt;
                    break;
                }
            }
        }

        return [
            'selector' => $selector,
            'found'    => $raw !== null,
            'record'   => $raw,
            'auto'     => filled($this->dkimSelector) ? false : true,
        ];
    }

    private function tryCommonDkimSelectors(): array
    {
        foreach (self::COMMON_SELECTORS as $selector) {
            $result = $this->queryDkim($selector);
            if ($result['found']) {
                return $result;
            }
        }

        return ['selector' => null, 'found' => false, 'record' => null, 'auto' => true];
    }
}
