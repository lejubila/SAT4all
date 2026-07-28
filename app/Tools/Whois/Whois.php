<?php

namespace App\Tools\Whois;

class Whois
{
    public const WHOIS_BIN = '/usr/bin/whois';
    public const MAX_LINES = 300;
    public const TIMEOUT   = 15;

    public static function validateTarget(string $target): bool
    {
        if (filter_var($target, FILTER_VALIDATE_IP)) {
            return true;
        }

        if (strlen($target) > 253) {
            return false;
        }

        // RFC 1123 hostname
        return (bool) preg_match(
            '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
            $target
        );
    }

    public function lookup(string $target): array
    {
        // For IPs, bypass the whois binary entirely — Alpine's whois client
        // sends a bogus second "domain <ip>" query that fails on most RIR servers.
        if (filter_var($target, FILTER_VALIDATE_IP)) {
            return $this->lookupIp($target);
        }

        if (! file_exists(self::WHOIS_BIN)) {
            return [
                'success' => false,
                'error'   => 'binary_missing',
                'output'  => [],
            ];
        }

        return $this->run([self::WHOIS_BIN, $target], self::TIMEOUT);
    }

    private function lookupIp(string $ip): array
    {
        $server = $this->getRirServer($ip);
        $result = $this->queryWhoisServer($server, $ip);

        // Follow one level of referral (ARIN → RIR, or RIPE delegations)
        foreach ($result['output'] as $line) {
            if (preg_match('/^ReferralServer:\s+whois:\/\/(\S+)/i', $line, $m)) {
                return $this->queryWhoisServer($m[1], $ip);
            }
            if (preg_match('/^refer:\s+(\S+)/i', $line, $m)) {
                return $this->queryWhoisServer($m[1], $ip);
            }
        }

        return $result;
    }

    private function queryWhoisServer(string $server, string $query): array
    {
        $socket = @fsockopen($server, 43, $errno, $errstr, self::TIMEOUT);
        if (! $socket) {
            return ['success' => false, 'error' => 'connection_failed', 'output' => [], 'truncated' => false];
        }

        stream_set_timeout($socket, self::TIMEOUT);
        fwrite($socket, $query . "\r\n");

        $raw = '';
        while (! feof($socket)) {
            $chunk = fread($socket, 4096);
            if ($chunk === false) {
                break;
            }
            $raw .= $chunk;
        }
        fclose($socket);

        $lines     = explode("\n", rtrim($raw));
        $truncated = false;
        if (count($lines) > self::MAX_LINES) {
            $lines     = array_slice($lines, 0, self::MAX_LINES);
            $truncated = true;
        }

        return [
            'success'   => count($lines) > 0,
            'exit_code' => 0,
            'output'    => $lines,
            'truncated' => $truncated,
            'error'     => null,
        ];
    }

    private function getRirServer(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 'whois.arin.net';
        }

        $octet = (int) explode('.', $ip)[0];

        // RIPE NCC — Europe, Middle East, Central Asia
        if (in_array($octet, [2, 5, 25, 31, 37, 46, 51, 57, 58, 59, 60, 62,
            77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92,
            93, 94, 95, 109, 176, 178, 185, 188, 193, 194, 195, 212, 213, 217], true)) {
            return 'whois.ripe.net';
        }

        // APNIC — Asia Pacific
        if (in_array($octet, [1, 14, 27, 36, 39, 42, 43, 49, 61, 101, 103,
            106, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121,
            122, 123, 124, 125, 126, 150, 153, 163, 171, 175, 180, 182, 183,
            202, 203, 210, 211, 218, 219, 220, 221, 222, 223], true)) {
            return 'whois.apnic.net';
        }

        // LACNIC — Latin America and Caribbean
        if (in_array($octet, [177, 179, 181, 186, 187, 189, 190, 191, 200, 201], true)) {
            return 'whois.lacnic.net';
        }

        // AFRINIC — Africa
        if (in_array($octet, [41, 102, 105, 154, 155, 156, 196, 197], true)) {
            return 'whois.afrinic.net';
        }

        // ARIN — North America and default for unknown ranges
        return 'whois.arin.net';
    }

    private function run(array $cmd, int $timeoutSeconds): array
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes);

        if (! is_resource($process)) {
            return ['success' => false, 'error' => 'process_failed', 'output' => []];
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdout    = '';
        $stderr    = '';
        $deadline  = microtime(true) + $timeoutSeconds;
        $lineCount = 0;
        $truncated = false;

        while (true) {
            $remaining = $deadline - microtime(true);
            if ($remaining <= 0) {
                proc_terminate($process, 15);
                usleep(200_000);
                proc_terminate($process, 9);
                break;
            }

            $read = [$pipes[1], $pipes[2]];
            $w    = null;
            $e    = null;
            $n    = stream_select($read, $w, $e, 0, 200_000);

            if ($n === false) {
                break;
            }

            foreach ($read as $pipe) {
                $chunk = fread($pipe, 4096);
                if ($chunk !== false && $chunk !== '') {
                    if ($pipe === $pipes[1]) {
                        $stdout .= $chunk;
                    } else {
                        $stderr .= $chunk;
                    }
                }
            }

            $status = proc_get_status($process);
            if (! $status['running']) {
                $stdout .= stream_get_contents($pipes[1]);
                $stderr .= stream_get_contents($pipes[2]);
                break;
            }
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        $lines = explode("\n", rtrim($stdout));

        if (count($lines) > self::MAX_LINES) {
            $lines     = array_slice($lines, 0, self::MAX_LINES);
            $truncated = true;
        }

        // Remove lines that are only comments or empty sequences at the top
        $lineCount = count($lines);

        return [
            'success'   => $exitCode === 0 || $lineCount > 0,
            'exit_code' => $exitCode,
            'output'    => $lines,
            'truncated' => $truncated,
            'error'     => $exitCode !== 0 && $lineCount === 0 ? 'lookup_failed' : null,
        ];
    }
}
