<?php

namespace App\Tools\PingTraceroute;

class PingTraceroute
{
    public const PING_BIN       = '/bin/ping';
    public const TRACEROUTE_BIN = '/usr/local/bin/traceroute';

    private const MAX_LINES = 100;

    /**
     * Valida il target: IPv4, IPv6 o hostname RFC 1123.
     * Non deve mai raggiungere exec() senza aver superato questo controllo.
     */
    public static function validateTarget(string $target): bool
    {
        if (filter_var($target, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        if (strlen($target) > 253) {
            return false;
        }

        return (bool) preg_match(
            '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)*'
            . '[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?$/',
            $target
        );
    }

    public static function isIpv6(string $target): bool
    {
        return filter_var($target, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * @param int $count 1–10
     * @return array{output: string, exit_code: int}
     */
    public function ping(string $target, int $count = 4): array
    {
        $count = max(1, min(10, $count));

        $cmd = [self::PING_BIN, '-c', (string) $count, '-W', '3', '-w', '20'];
        if (self::isIpv6($target)) {
            $cmd[] = '-6';
        }
        $cmd[] = $target;

        return $this->run($cmd, 25);
    }

    /**
     * @param int $maxHops 5|10|15|20|30
     * @return array{output: string, exit_code: int}
     */
    public function traceroute(string $target, int $maxHops = 20): array
    {
        $allowed = [5, 10, 15, 20, 30];
        if (!in_array($maxHops, $allowed, true)) {
            $maxHops = 20;
        }

        $cmd = [self::TRACEROUTE_BIN, '-m', (string) $maxHops, '-w', '2', '-q', '1'];
        if (self::isIpv6($target)) {
            $cmd[] = '-6';
        }
        $cmd[] = $target;

        return $this->run($cmd, 90);
    }

    /**
     * Esegue il comando tramite proc_open con array (nessuna shell, nessuna injection possibile).
     *
     * @param  string[] $cmd
     * @return array{output: string, exit_code: int}
     */
    private function run(array $cmd, int $timeoutSeconds): array
    {
        if (!is_executable($cmd[0])) {
            return ['output' => '', 'exit_code' => -1, 'error' => "Binary not found: {$cmd[0]}"];
        }

        $desc = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $proc = proc_open($cmd, $desc, $pipes);

        if (!is_resource($proc)) {
            return ['output' => '', 'exit_code' => -1, 'error' => 'Failed to start process'];
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output   = '';
        $lines    = 0;
        $deadline = microtime(true) + $timeoutSeconds;
        $timedOut = false;

        while (true) {
            $r = [$pipes[1], $pipes[2]];
            $w = $e = null;

            if (@stream_select($r, $w, $e, 1) > 0) {
                foreach ($r as $stream) {
                    $chunk = fread($stream, 4096);
                    if ($chunk !== false && $chunk !== '') {
                        $output .= $chunk;
                        $lines  += substr_count($chunk, "\n");
                    }
                }
            }

            $status = proc_get_status($proc);
            if (!$status['running']) {
                break;
            }
            if (microtime(true) > $deadline) {
                $timedOut = true;
                proc_terminate($proc, 15);
                usleep(200_000);
                proc_terminate($proc, 9);
                break;
            }
            if ($lines >= self::MAX_LINES) {
                proc_terminate($proc, 15);
                $output .= "\n[output truncated after " . self::MAX_LINES . " lines]";
                break;
            }
        }

        $output .= stream_get_contents($pipes[1]);
        $output .= stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($proc);

        if ($timedOut) {
            $output .= "\n[command timed out after {$timeoutSeconds}s]";
        }

        return [
            'output'    => rtrim($output),
            'exit_code' => $exitCode,
        ];
    }
}
