<?php

namespace App\Tools\SubnetCalculator;

/**
 * Logica pura per il calcolo di una subnet IPv4.
 *
 * Nessuna dipendenza da Laravel o dal layer HTTP: riceve un array con
 * 'ip' e 'cidr' gia' validati e ritorna l'esito del calcolo.
 */
class SubnetCalculator
{
    private string $ip;

    private int $cidr;

    /**
     * @param array{ip: string, cidr: int|string} $input
     */
    public function __construct(array $input)
    {
        $this->ip = $input['ip'];
        $this->cidr = (int) $input['cidr'];
    }

    /**
     * Calcola tutti i parametri della subnet.
     *
     * @return array<string, string|int|bool>
     */
    public function calculate(): array
    {
        $ipLong = ip2long($this->ip) & 0xFFFFFFFF;

        // Maschera di rete a partire dal prefisso CIDR (0..32).
        $mask = $this->cidr === 0
            ? 0
            : (~((1 << (32 - $this->cidr)) - 1)) & 0xFFFFFFFF;

        $wildcard = (~$mask) & 0xFFFFFFFF;
        $network = $ipLong & $mask;
        $broadcast = $network | $wildcard;
        $totalHosts = 2 ** (32 - $this->cidr);

        // Host utilizzabili e range: /31 (RFC 3021) e /32 sono casi speciali.
        if ($this->cidr >= 31) {
            $usableHosts = $this->cidr === 32 ? 1 : 2;
            $hostMin = $network;
            $hostMax = $broadcast;
        } else {
            $usableHosts = $totalHosts - 2;
            $hostMin = $network + 1;
            $hostMax = $broadcast - 1;
        }

        return [
            'ip'           => $this->ip,
            'cidr'         => $this->cidr,
            'netmask'      => long2ip($mask),
            'wildcard'     => long2ip($wildcard),
            'network'      => long2ip($network),
            'broadcast'    => long2ip($broadcast),
            'host_min'     => long2ip($hostMin),
            'host_max'     => long2ip($hostMax),
            'usable_hosts' => $usableHosts,
            'total_hosts'  => $totalHosts,
            'ip_class'     => $this->ipClass($ipLong),
            'is_private'   => $this->isPrivate($ipLong),
        ];
    }

    /**
     * Netmask equivalente per un dato prefisso CIDR (0..32).
     */
    public static function netmaskForPrefix(int $cidr): string
    {
        $mask = $cidr === 0
            ? 0
            : (~((1 << (32 - $cidr)) - 1)) & 0xFFFFFFFF;

        return long2ip($mask);
    }

    /**
     * Opzioni prefisso -> netmask per popolare la select (da /0 a /32).
     *
     * @return array<int, string>
     */
    public static function prefixOptions(): array
    {
        $options = [];

        for ($cidr = 0; $cidr <= 32; $cidr++) {
            $options[$cidr] = self::netmaskForPrefix($cidr);
        }

        return $options;
    }

    /**
     * Classe storica dell'indirizzo (A/B/C/D/E) dal primo ottetto.
     */
    private function ipClass(int $ipLong): string
    {
        $firstOctet = ($ipLong >> 24) & 0xFF;

        return match (true) {
            $firstOctet <= 127 => 'A',
            $firstOctet <= 191 => 'B',
            $firstOctet <= 223 => 'C',
            $firstOctet <= 239 => 'D',
            default            => 'E',
        };
    }

    /**
     * Vero se l'indirizzo ricade in un range privato (RFC 1918) o loopback/link-local.
     */
    private function isPrivate(int $ipLong): bool
    {
        $ranges = [
            ['10.0.0.0', 8],
            ['172.16.0.0', 12],
            ['192.168.0.0', 16],
            ['127.0.0.0', 8],      // loopback
            ['169.254.0.0', 16],   // link-local
        ];

        foreach ($ranges as [$base, $bits]) {
            $mask = (~((1 << (32 - $bits)) - 1)) & 0xFFFFFFFF;
            if (($ipLong & $mask) === ((ip2long($base) & 0xFFFFFFFF) & $mask)) {
                return true;
            }
        }

        return false;
    }
}
