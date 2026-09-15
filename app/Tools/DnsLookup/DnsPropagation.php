<?php

namespace App\Tools\DnsLookup;

/**
 * Verifica la propagazione DNS interrogando resolver pubblici in parallelo
 * tramite socket UDP grezzi (RFC 1035) — zero exec(), zero dipendenze esterne.
 */
class DnsPropagation
{
    private const TIMEOUT = 4;

    private const QTYPE_MAP = [
        'A'     => 1,
        'NS'    => 2,
        'CNAME' => 5,
        'MX'    => 15,
        'TXT'   => 16,
        'AAAA'  => 28,
    ];

    private const SERVERS = [
        // ── Americas ─────────────────────────────────────────────────────────
        ['ip' => '8.8.8.8',         'name' => 'Google',        'iso' => 'us', 'location' => 'United States'],
        ['ip' => '1.1.1.1',         'name' => 'Cloudflare',    'iso' => 'us', 'location' => 'United States'],
        ['ip' => '208.67.222.222',  'name' => 'OpenDNS',       'iso' => 'us', 'location' => 'United States'],
        ['ip' => '149.112.121.10',  'name' => 'CIRA',          'iso' => 'ca', 'location' => 'Canada'],
        ['ip' => '200.221.11.100',  'name' => 'Embratel',      'iso' => 'br', 'location' => 'Brazil'],
        // ── Europe ───────────────────────────────────────────────────────────
        ['ip' => '193.206.141.38',  'name' => 'GARR',          'iso' => 'it', 'location' => 'Italy'],
        ['ip' => '213.230.140.115', 'name' => 'TIM',           'iso' => 'it', 'location' => 'Italy'],
        ['ip' => '9.9.9.9',         'name' => 'Quad9',         'iso' => 'ch', 'location' => 'Switzerland'],
        ['ip' => '84.200.69.80',    'name' => 'DNS.WATCH',     'iso' => 'de', 'location' => 'Germany'],
        ['ip' => '80.67.169.40',    'name' => 'FDN',           'iso' => 'fr', 'location' => 'France'],
        ['ip' => '185.228.168.9',   'name' => 'CleanBrowsing', 'iso' => 'ie', 'location' => 'Ireland'],
        ['ip' => '77.88.8.8',       'name' => 'Yandex',        'iso' => 'ru', 'location' => 'Russia'],
        ['ip' => '94.140.14.14',    'name' => 'AdGuard',       'iso' => 'ru', 'location' => 'Russia'],
        // ── Middle East ──────────────────────────────────────────────────────
        ['ip' => '213.42.20.20',    'name' => 'Etisalat',      'iso' => 'ae', 'location' => 'UAE'],
        // ── Africa ───────────────────────────────────────────────────────────
        ['ip' => '196.25.1.1',      'name' => 'Telkom SA',     'iso' => 'za', 'location' => 'South Africa'],
        // ── Asia ─────────────────────────────────────────────────────────────
        ['ip' => '202.88.131.131',  'name' => 'TATA Comm',     'iso' => 'in', 'location' => 'India'],
        ['ip' => '114.114.114.114', 'name' => '114DNS',        'iso' => 'cn', 'location' => 'China'],
        ['ip' => '223.5.5.5',       'name' => 'AliDNS',        'iso' => 'cn', 'location' => 'China'],
        ['ip' => '119.29.29.29',    'name' => 'DNSPod',        'iso' => 'cn', 'location' => 'China'],
        ['ip' => '168.95.1.1',      'name' => 'HiNet',         'iso' => 'tw', 'location' => 'Taiwan'],
        ['ip' => '203.248.252.2',   'name' => 'KT',            'iso' => 'kr', 'location' => 'South Korea'],
        ['ip' => '210.188.224.10',  'name' => 'IIJ',           'iso' => 'jp', 'location' => 'Japan'],
        ['ip' => '202.46.32.20',    'name' => 'IRIX',          'iso' => 'sg', 'location' => 'Singapore'],
        ['ip' => '180.131.144.144', 'name' => 'Telkom',        'iso' => 'id', 'location' => 'Indonesia'],
        // ── Oceania ──────────────────────────────────────────────────────────
        ['ip' => '1.0.0.1',         'name' => 'Cloudflare',    'iso' => 'au', 'location' => 'Australia'],
    ];

    public static function supportedTypes(): array
    {
        return array_keys(self::QTYPE_MAP);
    }

    public function check(string $host, string $type): array
    {
        $type  = strtoupper($type);
        $qtype = self::QTYPE_MAP[$type] ?? 1;
        $packet = $this->buildQuery($host, $qtype);

        // ── Phase 1: open all sockets and send queries simultaneously ──────────
        $sockets = [];
        foreach (self::SERVERS as $i => $server) {
            $sock = @stream_socket_client(
                "udp://{$server['ip']}:53",
                $errno, $errstr,
                2,
                STREAM_CLIENT_CONNECT
            );
            if ($sock) {
                fwrite($sock, $packet);
                stream_set_blocking($sock, false);
                $sockets[$i] = $sock;
            }
        }

        // ── Phase 2: collect responses with stream_select ─────────────────────
        $raw       = [];
        $deadline  = microtime(true) + self::TIMEOUT;

        while ($sockets && microtime(true) < $deadline) {
            $read      = array_values($sockets);
            $write     = null;
            $except    = null;
            $remaining = $deadline - microtime(true);
            if ($remaining <= 0) break;

            $sec  = (int) $remaining;
            $usec = (int) (fmod($remaining, 1) * 1_000_000);

            $changed = @stream_select($read, $write, $except, $sec, $usec);
            if (! $changed) break;

            foreach ($read as $sock) {
                $idx = array_search($sock, $sockets, true);
                if ($idx !== false) {
                    $data = fread($sock, 4096);
                    fclose($sock);
                    unset($sockets[$idx]);
                    if ($data !== false && $data !== '') {
                        $raw[$idx] = $data;
                    }
                }
            }
        }

        foreach ($sockets as $sock) {
            fclose($sock);
        }

        // ── Phase 3: parse responses and build results ────────────────────────
        $results = [];
        foreach (self::SERVERS as $i => $server) {
            if (isset($raw[$i])) {
                $parsed    = $this->parseResponse($raw[$i], $qtype);
                $results[] = array_merge($server, $parsed);
            } else {
                $results[] = array_merge($server, [
                    'answers' => [],
                    'rcode'   => null,
                    'error'   => 'timeout',
                ]);
            }
        }

        return array_merge(['host' => $host, 'type' => $type], $this->consensus($results));
    }

    // ── Consensus ─────────────────────────────────────────────────────────────

    private function consensus(array $results): array
    {
        $freq = [];
        foreach ($results as $r) {
            if ($r['error'] !== null) continue;
            $key = implode('|', $r['answers']);
            $freq[$key] = ($freq[$key] ?? 0) + 1;
        }

        arsort($freq);
        $majorityKey     = array_key_first($freq) ?? '';
        $majorityAnswers = $majorityKey !== '' ? explode('|', $majorityKey) : [];

        $agreeing   = $freq[$majorityKey] ?? 0;
        $responding = array_sum($freq);
        $consistent = count($freq) <= 1;

        foreach ($results as &$r) {
            if ($r['error'] !== null) {
                $r['status'] = 'error';
            } elseif ($r['rcode'] === 'NXDOMAIN') {
                $r['status'] = 'nxdomain';
            } elseif (implode('|', $r['answers']) === $majorityKey) {
                $r['status'] = 'match';
            } else {
                $r['status'] = 'mismatch';
            }
        }
        unset($r);

        return [
            'results'    => $results,
            'consistent' => $consistent,
            'majority'   => $majorityAnswers,
            'agreeing'   => $agreeing,
            'responding' => $responding,
            'total'      => count($results),
        ];
    }

    // ── DNS packet builder ────────────────────────────────────────────────────

    private function buildQuery(string $host, int $qtype): string
    {
        $header = pack('nnnnnn',
            random_int(1, 65535), // ID
            0x0100,               // Flags: RD=1
            1,                    // QDCOUNT
            0, 0, 0               // AN/NS/ARCOUNT
        );

        $question = '';
        foreach (explode('.', rtrim($host, '.')) as $label) {
            $question .= chr(strlen($label)) . $label;
        }
        $question .= "\x00";
        $question .= pack('nn', $qtype, 1); // QTYPE, QCLASS=IN

        return $header . $question;
    }

    // ── DNS response parser ───────────────────────────────────────────────────

    private function parseResponse(string $data, int $qtype): array
    {
        if (strlen($data) < 12) {
            return ['answers' => [], 'rcode' => null, 'error' => 'short'];
        }

        $h     = unpack('nid/nflags/nqd/nan/nns/nar', substr($data, 0, 12));
        $rcode = $h['flags'] & 0x000F;

        if ($rcode === 3) {
            return ['answers' => [], 'rcode' => 'NXDOMAIN', 'error' => null];
        }
        if ($rcode !== 0) {
            return ['answers' => [], 'rcode' => "RCODE:{$rcode}", 'error' => null];
        }

        $offset = 12;

        // Skip question section
        for ($i = 0; $i < $h['qd']; $i++) {
            [, $offset] = $this->readName($data, $offset);
            $offset += 4;
        }

        $answers = [];
        for ($i = 0; $i < $h['an']; $i++) {
            if ($offset >= strlen($data)) break;
            [, $offset] = $this->readName($data, $offset);
            if ($offset + 10 > strlen($data)) break;

            $rr       = unpack('ntype/nclass/Nttl/nrdlen', substr($data, $offset, 10));
            $offset  += 10;
            $rdStart  = $offset;
            $rdata    = substr($data, $offset, $rr['rdlen']);
            $offset  += $rr['rdlen'];

            $value = $this->parseRdata($data, $rdStart, $rr['type'], $rdata);
            if ($value !== null && $value !== '') {
                $answers[] = $value;
            }
        }

        return ['answers' => $answers, 'rcode' => 'NOERROR', 'error' => null];
    }

    private function parseRdata(string $msg, int $rdOffset, int $type, string $rdata): ?string
    {
        return match ($type) {
            1  => strlen($rdata) === 4
                    ? implode('.', array_map('ord', str_split($rdata)))
                    : null,
            28 => strlen($rdata) === 16
                    ? inet_ntop($rdata)
                    : null,
            2, 5, 12 => $this->readName($msg, $rdOffset)[0],
            15 => sprintf('%d %s',
                    unpack('n', substr($rdata, 0, 2))[1],
                    $this->readName($msg, $rdOffset + 2)[0]
                 ),
            16 => $this->parseTxt($rdata),
            default => null,
        };
    }

    private function parseTxt(string $rdata): string
    {
        $out = '';
        $pos = 0;
        while ($pos < strlen($rdata)) {
            $len  = ord($rdata[$pos++]);
            $out .= substr($rdata, $pos, $len);
            $pos += $len;
        }
        return $out;
    }

    private function readName(string $data, int $offset): array
    {
        $name    = '';
        $jumped  = false;
        $maxLen  = strlen($data);
        $guard   = 0;
        $retOffset = $offset;

        while ($offset < $maxLen && $guard++ < 64) {
            $len = ord($data[$offset]);

            if ($len === 0) {
                if (! $jumped) $retOffset = $offset + 1;
                break;
            }

            if (($len & 0xC0) === 0xC0) {
                if ($offset + 1 >= $maxLen) break;
                if (! $jumped) $retOffset = $offset + 2;
                $ptr    = (($len & 0x3F) << 8) | ord($data[$offset + 1]);
                $offset = $ptr;
                $jumped = true;
                continue;
            }

            $offset++;
            if ($offset + $len > $maxLen) break;
            if ($name !== '') $name .= '.';
            $name   .= substr($data, $offset, $len);
            $offset += $len;

            if (! $jumped) $retOffset = $offset;
        }

        return [$name, $retOffset];
    }
}
