<?php

namespace App\Tools\CableSchemas;

/**
 * Dati statici per gli schemi cavi di rete RJ45.
 *
 * Nessuna logica di calcolo: restituisce i dataset per la renderizzazione
 * delle viste (standard T568A/B, cavi dritto/incrociato/rollover).
 */
class CableSchemas
{
    /**
     * Colori dei 8 pin per T568A e T568B (indice 0 = pin 1).
     *
     * Ogni voce: ['label' => 'stringa colore', 'tw' => 'classe Tailwind bg']
     *
     * @return array<string, array<int, array{label: string, tw: string}>>
     */
    public static function standards(): array
    {
        return [
            'T568A' => [
                ['label' => 'Bianco/Verde',    'tw' => 'bg-green-200  border-green-400'],
                ['label' => 'Verde',           'tw' => 'bg-green-500  border-green-700'],
                ['label' => 'Bianco/Arancio',  'tw' => 'bg-orange-200 border-orange-400'],
                ['label' => 'Blu',             'tw' => 'bg-blue-500   border-blue-700'],
                ['label' => 'Bianco/Blu',      'tw' => 'bg-blue-200   border-blue-400'],
                ['label' => 'Arancio',         'tw' => 'bg-orange-400 border-orange-600'],
                ['label' => 'Bianco/Marrone',  'tw' => 'bg-amber-200  border-amber-400'],
                ['label' => 'Marrone',         'tw' => 'bg-amber-700  border-amber-900'],
            ],
            'T568B' => [
                ['label' => 'Bianco/Arancio',  'tw' => 'bg-orange-200 border-orange-400'],
                ['label' => 'Arancio',         'tw' => 'bg-orange-400 border-orange-600'],
                ['label' => 'Bianco/Verde',    'tw' => 'bg-green-200  border-green-400'],
                ['label' => 'Blu',             'tw' => 'bg-blue-500   border-blue-700'],
                ['label' => 'Bianco/Blu',      'tw' => 'bg-blue-200   border-blue-400'],
                ['label' => 'Verde',           'tw' => 'bg-green-500  border-green-700'],
                ['label' => 'Bianco/Marrone',  'tw' => 'bg-amber-200  border-amber-400'],
                ['label' => 'Marrone',         'tw' => 'bg-amber-700  border-amber-900'],
            ],
        ];
    }

    /**
     * Mappatura pin per ogni tipo di cavo.
     *
     * 'ends' descrive i due connettori; 'map' indica a quale pin dell'estremo B
     * corrisponde ogni pin dell'estremo A (indice 0 = pin 1).
     *
     * @return array<string, array{standard_a: string, standard_b: string, map: array<int,int>}>
     */
    public static function cables(): array
    {
        return [
            'straight' => [
                'standard_a' => 'T568B',
                'standard_b' => 'T568B',
                'map'        => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            'crossover' => [
                'standard_a' => 'T568A',
                'standard_b' => 'T568B',
                'map'        => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            'rollover' => [
                'standard_a' => 'T568B',
                'standard_b' => 'T568B',
                'map'        => [8, 7, 6, 5, 4, 3, 2, 1],
            ],
        ];
    }
}
