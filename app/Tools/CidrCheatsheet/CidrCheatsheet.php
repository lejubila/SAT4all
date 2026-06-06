<?php

namespace App\Tools\CidrCheatsheet;

use App\Tools\SubnetCalculator\SubnetCalculator;

/**
 * Genera le 33 righe della CIDR cheat sheet (da /0 a /32).
 *
 * Riutilizza SubnetCalculator::netmaskForPrefix() per il calcolo delle maschere.
 * Logica pura: nessuna dipendenza da Laravel o dal layer HTTP.
 */
class CidrCheatsheet
{
    /**
     * @return array<int, array{
     *   cidr: int,
     *   netmask: string,
     *   wildcard: string,
     *   total: string,
     *   usable: string,
     *   note: string,
     * }>
     */
    public static function rows(): array
    {
        $rows = [];

        for ($cidr = 0; $cidr <= 32; $cidr++) {
            $netmask = SubnetCalculator::netmaskForPrefix($cidr);
            $wildcardLong = (~ip2long($netmask)) & 0xFFFFFFFF;
            $wildcard = long2ip($wildcardLong);
            $total = 2 ** (32 - $cidr);

            $usable = match (true) {
                $cidr === 32 => 1,
                $cidr === 31 => 2,
                default      => $total - 2,
            };

            $note = match ($cidr) {
                0  => 'default route',
                8  => 'classe A',
                16 => 'classe B',
                24 => 'classe C',
                31 => 'RFC 3021 (P2P)',
                32 => 'host singolo',
                default => '',
            };

            $rows[] = [
                'cidr'    => $cidr,
                'netmask' => $netmask,
                'wildcard'=> $wildcard,
                'total'   => number_format($total, 0, '.', ' '),
                'usable'  => number_format($usable, 0, '.', ' '),
                'note'    => $note,
            ];
        }

        return $rows;
    }
}
