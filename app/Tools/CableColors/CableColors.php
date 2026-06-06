<?php

namespace App\Tools\CableColors;

class CableColors
{
    /**
     * Definizioni colori per T568A e T568B (pin 1–8).
     *
     * Ogni entry contiene:
     *   - name_key : chiave di traduzione per il nome del colore
     *   - stripe   : se true, il filo è bianco con striscia colorata
     *   - swatch   : classe Tailwind bg-* per il colore base
     *   - text     : classe Tailwind text-* per leggibilità sul badge
     *   - pair     : numero della coppia (1–4)
     *   - function : funzione 10/100 Mb (tx+, tx-, rx+, unused…) e 1 Gb
     *
     * @return array{
     *   t568a: array<int, array{pin:int, name_key:string, stripe:bool, swatch:string, text:string, pair:int, func_fast:string, func_gig:string}>,
     *   t568b: array<int, array{pin:int, name_key:string, stripe:bool, swatch:string, text:string, pair:int, func_fast:string, func_gig:string}>
     * }
     */
    public static function standards(): array
    {
        $pinMeta = [
            1 => ['func_fast' => 'TX+', 'func_gig' => 'BI_DA+'],
            2 => ['func_fast' => 'TX−', 'func_gig' => 'BI_DA−'],
            3 => ['func_fast' => 'RX+', 'func_gig' => 'BI_DB+'],
            4 => ['func_fast' => '—',   'func_gig' => 'BI_DC+'],
            5 => ['func_fast' => '—',   'func_gig' => 'BI_DC−'],
            6 => ['func_fast' => 'RX−', 'func_gig' => 'BI_DB−'],
            7 => ['func_fast' => '—',   'func_gig' => 'BI_DD+'],
            8 => ['func_fast' => '—',   'func_gig' => 'BI_DD−'],
        ];

        $colors = [
            'white_green'  => ['name_key' => 'color_white_green',  'stripe' => true,  'swatch' => 'bg-green-400',  'text' => 'text-white', 'pair' => 3],
            'green'        => ['name_key' => 'color_green',         'stripe' => false, 'swatch' => 'bg-green-600',  'text' => 'text-white', 'pair' => 3],
            'white_orange' => ['name_key' => 'color_white_orange',  'stripe' => true,  'swatch' => 'bg-orange-400', 'text' => 'text-white', 'pair' => 2],
            'orange'       => ['name_key' => 'color_orange',        'stripe' => false, 'swatch' => 'bg-orange-500', 'text' => 'text-white', 'pair' => 2],
            'white_blue'   => ['name_key' => 'color_white_blue',    'stripe' => true,  'swatch' => 'bg-blue-400',   'text' => 'text-white', 'pair' => 1],
            'blue'         => ['name_key' => 'color_blue',          'stripe' => false, 'swatch' => 'bg-blue-600',   'text' => 'text-white', 'pair' => 1],
            'white_brown'  => ['name_key' => 'color_white_brown',   'stripe' => true,  'swatch' => 'bg-amber-600',  'text' => 'text-white', 'pair' => 4],
            'brown'        => ['name_key' => 'color_brown',         'stripe' => false, 'swatch' => 'bg-amber-800',  'text' => 'text-white', 'pair' => 4],
        ];

        $t568a_order = ['white_green', 'green', 'white_orange', 'blue', 'white_blue', 'orange', 'white_brown', 'brown'];
        $t568b_order = ['white_orange', 'orange', 'white_green', 'blue', 'white_blue', 'green', 'white_brown', 'brown'];

        $build = static function (array $order) use ($colors, $pinMeta): array {
            $result = [];
            foreach ($order as $i => $colorKey) {
                $pin = $i + 1;
                $result[] = array_merge(
                    ['pin' => $pin, 'color_key' => $colorKey],
                    $colors[$colorKey],
                    $pinMeta[$pin]
                );
            }
            return $result;
        };

        return [
            't568a' => $build($t568a_order),
            't568b' => $build($t568b_order),
        ];
    }

    /**
     * Pin che differiscono tra T568A e T568B (1, 2, 3, 6).
     *
     * @return int[]
     */
    public static function differingPins(): array
    {
        return [1, 2, 3, 6];
    }

    /**
     * Coppie con colore e nome chiave per la legenda.
     *
     * @return array<int, array{pair: int, name_key: string, swatch: string, text: string}>
     */
    public static function pairs(): array
    {
        return [
            ['pair' => 1, 'name_key' => 'pair_blue',   'swatch' => 'bg-blue-500',   'text' => 'text-white'],
            ['pair' => 2, 'name_key' => 'pair_orange',  'swatch' => 'bg-orange-500', 'text' => 'text-white'],
            ['pair' => 3, 'name_key' => 'pair_green',   'swatch' => 'bg-green-500',  'text' => 'text-white'],
            ['pair' => 4, 'name_key' => 'pair_brown',   'swatch' => 'bg-amber-700',  'text' => 'text-white'],
        ];
    }
}
