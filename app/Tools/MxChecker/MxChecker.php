<?php

namespace App\Tools\MxChecker;

class MxChecker
{
    private const SMTP_TIMEOUT = 3;

    private string $domain;

    public function __construct(string $input)
    {
        $this->domain = $this->normalizeDomain($input);
    }

    public function check(): array
    {
        $mxRecords = $this->queryMx();
        $servers   = [];

        foreach ($mxRecords as $mx) {
            $ips  = $this->resolveIps($mx['host']);
            $smtp = $this->testSmtp($mx['host']);

            $servers[] = [
                'priority' => $mx['priority'],
                'host'     => $mx['host'],
                'ips'      => $ips,
                'smtp'     => $smtp,
            ];
        }

        return [
            'domain'   => $this->domain,
            'mx_count' => count($servers),
            'servers'  => $servers,
        ];
    }

    // ── Domain normalisation ──────────────────────────────────────────────────

    private function normalizeDomain(string $input): string
    {
        $input = trim($input);

        if (str_contains($input, '@')) {
            $input = substr($input, strpos($input, '@') + 1);
        }

        $input = preg_replace('~^https?://~i', '', $input) ?? $input;
        $input = explode('/', $input)[0];

        return strtolower(rtrim($input, '.'));
    }

    // ── DNS queries ───────────────────────────────────────────────────────────

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

    private function resolveIps(string $host): array
    {
        $records = @dns_get_record($host, DNS_A);
        if (empty($records)) {
            return [];
        }

        return array_values(array_filter(array_map(fn ($r) => $r['ip'] ?? null, $records)));
    }

    // ── SMTP probe ────────────────────────────────────────────────────────────

    private function testSmtp(string $host): array
    {
        $start = microtime(true);
        $fp    = @stream_socket_client(
            "tcp://{$host}:25",
            $errno,
            $errstr,
            self::SMTP_TIMEOUT
        );

        if (! $fp) {
            return [
                'reachable'  => false,
                'latency_ms' => null,
                'banner'     => null,
                'starttls'   => false,
                'auth'       => null,
                'size'       => null,
                'ehlo_lines' => [],
            ];
        }

        $latencyMs = round((microtime(true) - $start) * 1000, 1);
        stream_set_timeout($fp, self::SMTP_TIMEOUT);

        // Read greeting (may be multi-line 220-)
        $banner = '';
        while ($line = fgets($fp, 512)) {
            $text = rtrim(substr($line, 4));
            if ($banner === '') {
                $banner = $text;
            }
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
            $meta = stream_get_meta_data($fp);
            if ($meta['timed_out']) {
                break;
            }
        }

        // Send EHLO
        fwrite($fp, "EHLO mx-checker.local\r\n");

        $ehloLines = [];
        while ($line = fgets($fp, 512)) {
            $text = rtrim(substr($line, 4));
            if (filled($text)) {
                $ehloLines[] = $text;
            }
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
            $meta = stream_get_meta_data($fp);
            if ($meta['timed_out']) {
                break;
            }
        }

        @fwrite($fp, "QUIT\r\n");
        @fclose($fp);

        return [
            'reachable'  => true,
            'latency_ms' => $latencyMs,
            'banner'     => $banner ?: null,
            'starttls'   => $this->hasCapability($ehloLines, 'STARTTLS'),
            'auth'       => $this->extractAuth($ehloLines),
            'size'       => $this->extractSize($ehloLines),
            'ehlo_lines' => $ehloLines,
        ];
    }

    // ── EHLO capability helpers ───────────────────────────────────────────────

    private function hasCapability(array $lines, string $keyword): bool
    {
        foreach ($lines as $line) {
            if (stripos($line, $keyword) === 0) {
                return true;
            }
        }
        return false;
    }

    private function extractAuth(array $lines): ?string
    {
        foreach ($lines as $line) {
            if (stripos($line, 'AUTH') === 0) {
                $parts = preg_split('/\s+/', $line, 2);
                return isset($parts[1]) ? trim($parts[1]) : null;
            }
        }
        return null;
    }

    private function extractSize(array $lines): ?int
    {
        foreach ($lines as $line) {
            if (stripos($line, 'SIZE') === 0) {
                $parts = preg_split('/\s+/', $line, 2);
                $val   = isset($parts[1]) ? (int) trim($parts[1]) : 0;
                return $val > 0 ? $val : null;
            }
        }
        return null;
    }
}
