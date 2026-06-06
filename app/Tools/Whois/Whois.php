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
        if (! file_exists(self::WHOIS_BIN)) {
            return [
                'success' => false,
                'error'   => 'binary_missing',
                'output'  => [],
            ];
        }

        $cmd = [self::WHOIS_BIN, $target];

        return $this->run($cmd, self::TIMEOUT);
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
