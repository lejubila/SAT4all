<?php

namespace App\Tools\Ipv6Calculator;

/**
 * Logica pura per il calcolo di una subnet IPv6.
 *
 * Lavora direttamente sui 16 byte restituiti da inet_pton, senza dipendere
 * da estensioni big-number (gmp/bcmath). Riceve 'address' e 'prefix' gia'
 * validati e ritorna l'esito del calcolo.
 */
class Ipv6Calculator
{
    private string $address;

    private int $prefix;

    /**
     * @param array{address: string, prefix: int|string} $input
     */
    public function __construct(array $input)
    {
        $this->address = $input['address'];
        $this->prefix = (int) $input['prefix'];
    }

    /**
     * @return array<string, string|int>
     */
    public function calculate(): array
    {
        $bytes = array_values(unpack('C*', inet_pton($this->address)));
        $mask = $this->maskBytes($this->prefix);

        $network = [];
        $last = [];
        for ($i = 0; $i < 16; $i++) {
            $network[$i] = $bytes[$i] & $mask[$i];
            $last[$i] = $network[$i] | ((~$mask[$i]) & 0xFF);
        }

        $networkBin = pack('C*', ...$network);
        $lastBin = pack('C*', ...$last);

        return [
            'address'          => $this->address,
            'prefix'           => $this->prefix,
            'compressed'       => inet_ntop(inet_pton($this->address)),
            'expanded'         => $this->expand($bytes),
            'network'          => inet_ntop($networkBin),
            'network_expanded' => $this->expand($network),
            'prefix_notation'  => inet_ntop($networkBin).'/'.$this->prefix,
            'first_address'    => inet_ntop($networkBin),
            'last_address'     => inet_ntop($lastBin),
            'total_addresses'  => self::powerOfTwo(128 - $this->prefix),
            'type'             => $this->addressType($bytes),
        ];
    }

    /**
     * Maschera di rete come array di 16 byte per il prefisso dato (0..128).
     *
     * @return array<int, int>
     */
    private function maskBytes(int $prefix): array
    {
        $mask = array_fill(0, 16, 0);
        $fullBytes = intdiv($prefix, 8);
        $remBits = $prefix % 8;

        for ($i = 0; $i < $fullBytes; $i++) {
            $mask[$i] = 0xFF;
        }

        if ($remBits > 0 && $fullBytes < 16) {
            $mask[$fullBytes] = (0xFF << (8 - $remBits)) & 0xFF;
        }

        return $mask;
    }

    /**
     * Forma estesa (8 gruppi da 4 cifre esadecimali) a partire dai 16 byte.
     *
     * @param array<int, int> $bytes
     */
    private function expand(array $bytes): string
    {
        $hex = '';
        foreach ($bytes as $byte) {
            $hex .= str_pad(dechex($byte), 2, '0', STR_PAD_LEFT);
        }

        return implode(':', str_split($hex, 4));
    }

    /**
     * Categoria dell'indirizzo (chiave i18n: tools.ipv6_calculator.type_*).
     *
     * @param array<int, int> $bytes
     */
    private function addressType(array $bytes): string
    {
        $isZeroUpTo15 = array_sum(array_slice($bytes, 0, 15)) === 0;

        return match (true) {
            array_sum($bytes) === 0                      => 'unspecified',
            $isZeroUpTo15 && $bytes[15] === 1            => 'loopback',
            $bytes[0] === 0xFF                           => 'multicast',
            $bytes[0] === 0xFE && ($bytes[1] & 0xC0) === 0x80 => 'link_local',
            ($bytes[0] & 0xFE) === 0xFC                  => 'unique_local',
            default                                      => 'global_unicast',
        };
    }

    /**
     * 2^exp come stringa decimale (senza estensioni big-number).
     */
    private static function powerOfTwo(int $exp): string
    {
        $result = '1';

        for ($i = 0; $i < $exp; $i++) {
            $carry = 0;
            $out = '';
            for ($j = strlen($result) - 1; $j >= 0; $j--) {
                $doubled = ((int) $result[$j]) * 2 + $carry;
                $out = ($doubled % 10).$out;
                $carry = intdiv($doubled, 10);
            }
            if ($carry > 0) {
                $out = $carry.$out;
            }
            $result = $out;
        }

        return $result;
    }

    /**
     * Opzioni di prefisso (da /0 a /128) per popolare la select.
     *
     * @return array<int, int>
     */
    public static function prefixOptions(): array
    {
        return range(0, 128);
    }
}
