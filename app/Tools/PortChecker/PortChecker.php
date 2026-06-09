<?php

namespace App\Tools\PortChecker;

class PortChecker
{
    public const TIMEOUT = 5;

    public function check(string $host, int $port, string $protocol): array
    {
        $base = [
            'host'     => $host,
            'port'     => $port,
            'protocol' => strtoupper($protocol),
        ];

        return array_merge($base, $protocol === 'udp'
            ? $this->checkUdp($host, $port)
            : $this->checkTcp($host, $port));
    }

    private function checkTcp(string $host, int $port): array
    {
        $start = microtime(true);

        $fp = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            self::TIMEOUT,
        );

        $ms = round((microtime(true) - $start) * 1000);

        if ($fp) {
            fclose($fp);
            return ['status' => 'open', 'latency_ms' => $ms];
        }

        // ECONNREFUSED → closed; timeout (ETIMEDOUT / 110) → filtered
        $status = ($errno === 111 || $errno === 61) ? 'closed' : 'filtered';

        return ['status' => $status, 'latency_ms' => null];
    }

    private function checkUdp(string $host, int $port): array
    {
        $fp = @stream_socket_client(
            "udp://{$host}:{$port}",
            $errno,
            $errstr,
            self::TIMEOUT,
        );

        if (! $fp) {
            return ['status' => 'filtered', 'latency_ms' => null, 'udp_note' => true];
        }

        stream_set_timeout($fp, self::TIMEOUT);

        @fwrite($fp, "\x00");

        // On Linux, if the kernel received ICMP port-unreachable, the next
        // read will fail with ECONNREFUSED; otherwise it times out (open|filtered).
        $data = @fread($fp, 1);
        $info = stream_get_meta_data($fp);
        fclose($fp);

        if ($info['timed_out']) {
            return ['status' => 'open_filtered', 'latency_ms' => null, 'udp_note' => true];
        }

        // Error on read → ICMP unreachable → port closed
        return ['status' => $data === false ? 'closed' : 'open_filtered', 'latency_ms' => null, 'udp_note' => true];
    }

    public static function validateHost(string $host): bool
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return true;
        }

        if (strlen($host) > 253) {
            return false;
        }

        return (bool) preg_match(
            '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
            $host,
        );
    }
}
