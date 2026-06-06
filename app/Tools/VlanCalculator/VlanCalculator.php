<?php

namespace App\Tools\VlanCalculator;

use App\Tools\SubnetCalculator\SubnetCalculator;

/**
 * Suddivide una rete base in subnet di pari dimensione e assegna VLAN ID progressivi.
 *
 * Logica pura: nessuna dipendenza da Laravel o HTTP.
 */
class VlanCalculator
{
    public const MAX_RESULTS = 256;

    private string $network;

    private int $baseCidr;

    private int $subnetCidr;

    private int $startVlan;

    /**
     * @param array{network: string, base_cidr: int|string, subnet_cidr: int|string, start_vlan: int|string} $input
     */
    public function __construct(array $input)
    {
        $this->network    = $input['network'];
        $this->baseCidr   = (int) $input['base_cidr'];
        $this->subnetCidr = (int) $input['subnet_cidr'];
        $this->startVlan  = (int) $input['start_vlan'];
    }

    /**
     * Calcola le subnet / VLAN.
     *
     * @return array{
     *   base_network: string,
     *   base_cidr: int,
     *   subnet_cidr: int,
     *   start_vlan: int,
     *   total_subnets: int,
     *   shown: int,
     *   truncated: bool,
     *   vlans: array<int, array{vlan_id: int, network: string, broadcast: string, gateway: string, netmask: string, usable: int}>
     * }
     */
    public function calculate(): array
    {
        $hostBits  = 32 - $this->subnetCidr;
        $subnetSize = 2 ** $hostBits;
        $totalSubnets = 2 ** ($this->subnetCidr - $this->baseCidr);

        // Normalizza l'indirizzo di rete (annulla i bit host).
        $mask        = (~((1 << (32 - $this->baseCidr)) - 1)) & 0xFFFFFFFF;
        $baseNetLong = ip2long($this->network) & $mask;

        $shown   = min($totalSubnets, self::MAX_RESULTS);
        $vlans   = [];
        $vlanId  = $this->startVlan;

        for ($i = 0; $i < $shown; $i++) {
            $netLong   = ($baseNetLong + $i * $subnetSize) & 0xFFFFFFFF;
            $broadLong = ($netLong + $subnetSize - 1) & 0xFFFFFFFF;

            $usable  = max(0, $subnetSize - 2);
            $gateway = $usable > 0 ? long2ip($netLong + 1) : long2ip($netLong);

            $vlans[] = [
                'vlan_id'   => $vlanId,
                'network'   => long2ip($netLong).'/'.$this->subnetCidr,
                'broadcast' => long2ip($broadLong),
                'gateway'   => $gateway,
                'netmask'   => SubnetCalculator::netmaskForPrefix($this->subnetCidr),
                'usable'    => $usable,
            ];

            $vlanId++;
            // VLAN ID 1-4094; wrap se si supera 4094.
            if ($vlanId > 4094) {
                $vlanId = 1;
            }
        }

        return [
            'base_network'  => long2ip($baseNetLong).'/'.$this->baseCidr,
            'base_cidr'     => $this->baseCidr,
            'subnet_cidr'   => $this->subnetCidr,
            'start_vlan'    => $this->startVlan,
            'total_subnets' => $totalSubnets,
            'shown'         => $shown,
            'truncated'     => $totalSubnets > self::MAX_RESULTS,
            'vlans'         => $vlans,
        ];
    }
}
