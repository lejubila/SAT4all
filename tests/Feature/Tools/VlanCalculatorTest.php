<?php

namespace Tests\Feature\Tools;

use App\Tools\VlanCalculator\VlanCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VlanCalculatorTest extends TestCase
{
    public function test_shows_calculator_page(): void
    {
        $this->get(route('tools.vlan-calculator.index'))
            ->assertOk()
            ->assertSee(__('tools.vlan_calculator.title'));
    }

    public function test_calculates_vlan_allocation(): void
    {
        $this->post(route('tools.vlan-calculator.calculate'), [
            'network'     => '10.0.0.0',
            'base_cidr'   => 8,
            'subnet_cidr' => 24,
            'start_vlan'  => 10,
        ])
            ->assertOk()
            ->assertSee('10.0.0.0/24')    // prima subnet
            ->assertSee('10.0.0.1')       // primo gateway
            ->assertSee('10.0.1.0/24')    // seconda subnet
            ->assertSee('10')             // VLAN ID iniziale
            ->assertSee('254');           // host utilizzabili per /24
    }

    public function test_subnet_cidr_must_be_greater_than_base_cidr(): void
    {
        $this->post(route('tools.vlan-calculator.calculate'), [
            'network'     => '10.0.0.0',
            'base_cidr'   => 24,
            'subnet_cidr' => 16,
            'start_vlan'  => 1,
        ])
            ->assertOk()
            ->assertSee(__('tools.vlan_calculator.error_subnet_too_small'));
    }

    public function test_invalid_ip_returns_error(): void
    {
        $this->post(route('tools.vlan-calculator.calculate'), [
            'network'     => 'not-an-ip',
            'base_cidr'   => 8,
            'subnet_cidr' => 24,
            'start_vlan'  => 1,
        ])
            ->assertOk()
            ->assertSee(__('tools.vlan_calculator.error_network_invalid'));
    }

    public function test_invalid_vlan_id_returns_error(): void
    {
        $this->post(route('tools.vlan-calculator.calculate'), [
            'network'     => '10.0.0.0',
            'base_cidr'   => 8,
            'subnet_cidr' => 24,
            'start_vlan'  => 5000,
        ])
            ->assertOk()
            ->assertSee(__('tools.vlan_calculator.error_start_vlan_range'));
    }

    /**
     * @param  array<string, mixed>  $expected
     */
    #[DataProvider('vlanProvider')]
    public function test_logic_computes_expected_values(array $input, array $expected): void
    {
        $result = (new VlanCalculator($input))->calculate();

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $result[$key], "Campo {$key}");
        }
    }

    /**
     * @return array<string, array{0: array<string,mixed>, 1: array<string,mixed>}>
     */
    public static function vlanProvider(): array
    {
        return [
            '192.168.0.0/16 diviso in /24' => [
                ['network' => '192.168.0.0', 'base_cidr' => 16, 'subnet_cidr' => 24, 'start_vlan' => 100],
                ['total_subnets' => 256, 'shown' => 256, 'truncated' => false, 'base_network' => '192.168.0.0/16'],
            ],
            '10.0.0.0/8 diviso in /24 (troncato)' => [
                ['network' => '10.0.0.0', 'base_cidr' => 8, 'subnet_cidr' => 24, 'start_vlan' => 1],
                ['total_subnets' => 65536, 'shown' => 256, 'truncated' => true],
            ],
            'prima subnet corretta' => [
                ['network' => '172.16.0.0', 'base_cidr' => 16, 'subnet_cidr' => 24, 'start_vlan' => 200],
                ['base_network' => '172.16.0.0/16'],
            ],
        ];
    }

    public function test_first_vlan_entry_is_correct(): void
    {
        $result = (new VlanCalculator([
            'network'     => '192.168.0.0',
            'base_cidr'   => 16,
            'subnet_cidr' => 24,
            'start_vlan'  => 10,
        ]))->calculate();

        $first = $result['vlans'][0];
        $this->assertSame(10,              $first['vlan_id']);
        $this->assertSame('192.168.0.0/24',$first['network']);
        $this->assertSame('192.168.0.1',   $first['gateway']);
        $this->assertSame('192.168.0.255', $first['broadcast']);
        $this->assertSame(254,             $first['usable']);
    }
}
