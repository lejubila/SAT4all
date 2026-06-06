<?php

namespace App\Tools\DnsLookup;

/**
 * Esegue query DNS usando le funzioni native PHP (dns_get_record, gethostbyaddr).
 *
 * Nessun exec() — nessun input utente passato alla shell.
 * Logica pura: nessuna dipendenza da Laravel o HTTP.
 */
class DnsLookup
{
    /** Tipi di record supportati con il relativo flag PHP. */
    private const TYPE_MAP = [
        'A'     => DNS_A,
        'AAAA'  => DNS_AAAA,
        'MX'    => DNS_MX,
        'NS'    => DNS_NS,
        'TXT'   => DNS_TXT,
        'CNAME' => DNS_CNAME,
        'SOA'   => DNS_SOA,
        'PTR'   => DNS_PTR,
        'ALL'   => DNS_ALL,
    ];

    private string $host;

    private string $type;

    /**
     * @param array{host: string, type: string} $input
     */
    public function __construct(array $input)
    {
        $this->host = trim($input['host']);
        $this->type = strtoupper($input['type']);
    }

    /**
     * @return array{
     *   host: string,
     *   type: string,
     *   records: array<int, array<string, string>>,
     *   count: int,
     *   error: string|null,
     * }
     */
    public function lookup(): array
    {
        $host = $this->resolveHost();
        $flag = self::TYPE_MAP[$this->type] ?? DNS_A;

        // dns_get_record restituisce false in caso di errore di rete o NXDOMAIN.
        $raw = @dns_get_record($host, $flag);

        if ($raw === false || $raw === []) {
            return [
                'host'    => $this->host,
                'type'    => $this->type,
                'records' => [],
                'count'   => 0,
                'error'   => null,
            ];
        }

        return [
            'host'    => $this->host,
            'type'    => $this->type,
            'records' => array_map([$this, 'normalize'], $raw),
            'count'   => count($raw),
            'error'   => null,
        ];
    }

    /**
     * Per query PTR su IPv4/IPv6 converte l'IP nel formato in-addr.arpa / ip6.arpa.
     * Per tutti gli altri tipi lascia il valore invariato.
     */
    private function resolveHost(): string
    {
        if ($this->type === 'PTR' && filter_var($this->host, FILTER_VALIDATE_IP)) {
            if (filter_var($this->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                return $this->ipv6ToArpa($this->host);
            }

            return implode('.', array_reverse(explode('.', $this->host))).'.in-addr.arpa';
        }

        return $this->host;
    }

    /**
     * Normalizza un record raw di dns_get_record in un array piatto chiave→valore.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, string>
     */
    private function normalize(array $raw): array
    {
        $type = strtoupper($raw['type'] ?? $this->type);
        $ttl  = isset($raw['ttl']) ? (string) $raw['ttl'].'s' : '—';

        $data = match ($type) {
            'A'     => ['ip'  => $raw['ip']   ?? ''],
            'AAAA'  => ['ipv6'=> $raw['ipv6'] ?? ''],
            'MX'    => ['pri' => (string) ($raw['pri'] ?? ''), 'target' => $raw['target'] ?? ''],
            'NS'    => ['target'  => $raw['target']  ?? ''],
            'CNAME' => ['target'  => $raw['target']  ?? ''],
            'PTR'   => ['target'  => $raw['target']  ?? ''],
            'TXT'   => ['txt'     => $raw['txt'] ?? (isset($raw['entries']) ? implode(' ', $raw['entries']) : '')],
            'SOA'   => [
                'mname'   => $raw['mname']   ?? '',
                'rname'   => $raw['rname']   ?? '',
                'serial'  => (string) ($raw['serial']  ?? ''),
                'refresh' => (string) ($raw['refresh'] ?? ''),
                'retry'   => (string) ($raw['retry']   ?? ''),
                'expire'  => (string) ($raw['expire']  ?? ''),
                'minttl'  => (string) ($raw['minimum-ttl'] ?? ''),
            ],
            default => array_filter(
                $raw,
                fn ($v, $k) => ! in_array($k, ['host', 'class', 'ttl', 'type'], true) && is_scalar($v),
                ARRAY_FILTER_USE_BOTH
            ),
        };

        return array_merge(['type' => $type, 'ttl' => $ttl], array_map('strval', $data));
    }

    /**
     * Converte un IPv6 in notazione ip6.arpa per query PTR.
     */
    private function ipv6ToArpa(string $ip): string
    {
        $packed  = inet_pton($ip);
        $hex     = bin2hex($packed);
        $nibbles = array_reverse(str_split($hex));

        return implode('.', $nibbles).'.ip6.arpa';
    }

    /**
     * @return array<int, string>
     */
    public static function recordTypes(): array
    {
        return array_keys(self::TYPE_MAP);
    }
}
