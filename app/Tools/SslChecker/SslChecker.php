<?php

namespace App\Tools\SslChecker;

class SslChecker
{
    private const TIMEOUT   = 10;
    private const WARN_DAYS = 30;

    public function check(string $host, int $port = 443): array
    {
        if (! extension_loaded('openssl')) {
            return $this->err($host, $port, 'OpenSSL extension is not available.');
        }

        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'SNI_enabled'       => true,
                'peer_name'         => $host,
            ],
        ]);

        $errno  = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            self::TIMEOUT,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! $socket) {
            return $this->err($host, $port, $errstr ?: "Connection failed (errno {$errno})");
        }

        $meta   = stream_get_meta_data($socket);
        $crypto = $meta['crypto'] ?? [];

        $params       = stream_context_get_params($socket);
        $certResource = $params['options']['ssl']['peer_certificate'] ?? null;
        fclose($socket);

        if (! $certResource) {
            return $this->err($host, $port, 'Could not retrieve certificate.');
        }

        $info = openssl_x509_parse($certResource);
        if (! $info) {
            return $this->err($host, $port, 'Could not parse certificate.');
        }

        $validFrom = (int) ($info['validFrom_time_t'] ?? 0);
        $validTo   = (int) ($info['validTo_time_t'] ?? 0);
        $now       = time();
        $daysLeft  = (int) ceil(($validTo - $now) / 86400);
        $expired   = $validTo < $now;
        $expiring  = ! $expired && $daysLeft <= self::WARN_DAYS;

        return [
            'host'      => $host,
            'port'      => $port,
            'connected' => true,
            'error'     => null,
            'trusted'   => $this->checkTrust($host, $port),
            'expired'   => $expired,
            'expiring'  => $expiring,
            'days_left' => $daysLeft,
            'cert'      => [
                'subject'    => $info['subject'] ?? [],
                'issuer'     => $info['issuer'] ?? [],
                'valid_from' => date('Y-m-d H:i:s \U\T\C', $validFrom),
                'valid_to'   => date('Y-m-d H:i:s \U\T\C', $validTo),
                'sans'       => $this->parseSans($info['extensions']['subjectAltName'] ?? ''),
                'serial'     => strtoupper($info['serialNumberHex'] ?? ''),
                'fingerprint' => $this->fingerprint($certResource),
            ],
            'tls' => [
                'protocol' => $crypto['protocol'] ?? ($crypto['cipher_version'] ?? 'unknown'),
                'cipher'   => $crypto['cipher_name'] ?? ($crypto['cipher_algorithm'] ?? 'unknown'),
                'bits'     => (int) ($crypto['bits'] ?? ($crypto['cipher_bits'] ?? 0)),
            ],
        ];
    }

    private function checkTrust(string $host, int $port): ?bool
    {
        $candidates = [
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/ca-bundle.pem',
        ];

        $caFile = null;
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $caFile = $path;
                break;
            }
        }

        if ($caFile === null) {
            return null;
        }

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
                'SNI_enabled'      => true,
                'peer_name'        => $host,
                'cafile'           => $caFile,
            ],
        ]);

        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            self::TIMEOUT,
            STREAM_CLIENT_CONNECT,
            $context
        );

        $ok = $socket !== false;
        if ($socket) {
            fclose($socket);
        }
        return $ok;
    }

    private function parseSans(string $sanStr): array
    {
        if ($sanStr === '') {
            return [];
        }
        preg_match_all('/DNS:([^,\s]+)/i', $sanStr, $m);
        return $m[1] ?? [];
    }

    private function fingerprint($cert): string
    {
        openssl_x509_export($cert, $pem);
        $der = base64_decode(preg_replace('/-----[^-]+-----|[\r\n\s]/', '', $pem));
        $raw = hash('sha256', $der, true);
        return implode(':', str_split(strtoupper(bin2hex($raw)), 2));
    }

    private function err(string $host, int $port, string $message): array
    {
        return [
            'host'      => $host,
            'port'      => $port,
            'connected' => false,
            'error'     => $message,
        ];
    }
}
