<?php

namespace App\Tools\EmailHeaderAnalyzer;

class EmailHeaderAnalyzer
{
    private string $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
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
        $from = '';
        $by   = '';

        if (preg_match('/\bfrom\s+(\S+)/i', $value, $m)) {
            $from = $m[1];
        }
        if (preg_match('/\bby\s+(\S+)/i', $value, $m)) {
            $by = $m[1];
        }

        // Timestamp follows the last semicolon
        $unix      = null;
        $timestamp = '';
        $semi      = strrpos($value, ';');
        if ($semi !== false) {
            $dateStr   = trim(substr($value, $semi + 1));
            $parsed    = strtotime($dateStr);
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
        $raw = null;
        foreach ($headers as $h) {
            if (strtolower($h['name']) === 'authentication-results') {
                $raw = $h['value'];
                break;
            }
        }

        if ($raw === null) {
            return ['spf' => null, 'dkim' => null, 'dmarc' => null, 'raw' => null];
        }

        return array_merge($this->parseAuth($raw), ['raw' => $raw]);
    }

    private function parseAuth(string $value): array
    {
        $result = ['spf' => null, 'dkim' => null, 'dmarc' => null];

        foreach (array_keys($result) as $proto) {
            if (preg_match('/\b' . $proto . '=(\S+)/i', $value, $m)) {
                $result[$proto] = strtolower(rtrim($m[1], ';,'));
            }
        }

        return $result;
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
