<?php

namespace Tests\Feature\Tools;

use App\Tools\SubnetCalculator\SubnetCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SubnetCalculatorTest extends TestCase
{
    public function test_shows_calculator_page(): void
    {
        $this->get(route('tools.subnet-calculator.index'))
            ->assertOk()
            ->assertSee(__('tools.subnet_calculator.title'))
            ->assertSee('/24 — 255.255.255.0')   // opzione della select prefisso/netmask
            ->assertSee('/30 — 255.255.255.252');
    }

    public function test_prefix_options_cover_all_prefixes(): void
    {
        $options = SubnetCalculator::prefixOptions();

        $this->assertCount(33, $options);            // da /0 a /32
        $this->assertSame('0.0.0.0', $options[0]);
        $this->assertSame('255.255.255.0', $options[24]);
        $this->assertSame('255.255.255.255', $options[32]);
    }

    public function test_calculates_subnet_correctly(): void
    {
        $this->post(route('tools.subnet-calculator.calculate'), [
            'ip'   => '192.168.1.10',
            'cidr' => 24,
        ])
            ->assertOk()
            ->assertSee('192.168.1.0/24')   // rete
            ->assertSee('192.168.1.255')    // broadcast
            ->assertSee('255.255.255.0')    // netmask
            ->assertSee('192.168.1.1')      // primo host
            ->assertSee('192.168.1.254');   // ultimo host
    }

    public function test_invalid_ip_returns_validation_error(): void
    {
        $this->post(route('tools.subnet-calculator.calculate'), [
            'ip'   => '999.1.1.1',
            'cidr' => 24,
        ])
            ->assertOk()
            ->assertSee(__('tools.subnet_calculator.error_ip_invalid'));
    }

    public function test_cidr_out_of_range_returns_validation_error(): void
    {
        $this->post(route('tools.subnet-calculator.calculate'), [
            'ip'   => '10.0.0.1',
            'cidr' => 40,
        ])
            ->assertOk()
            ->assertSee(__('tools.subnet_calculator.error_cidr_range'));
    }

    /**
     * @param  array<string, mixed>  $expected
     */
    #[DataProvider('subnetProvider')]
    public function test_logic_class_computes_expected_values(string $ip, int $cidr, array $expected): void
    {
        $result = (new SubnetCalculator(['ip' => $ip, 'cidr' => $cidr]))->calculate();

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $result[$key], "Campo {$key}");
        }
    }

    /**
     * @return array<string, array{0: string, 1: int, 2: array<string, mixed>}>
     */
    public static function subnetProvider(): array
    {
        return [
            'classico /24' => ['192.168.1.10', 24, [
                'network'      => '192.168.1.0',
                'broadcast'    => '192.168.1.255',
                'netmask'      => '255.255.255.0',
                'wildcard'     => '0.0.0.255',
                'host_min'     => '192.168.1.1',
                'host_max'     => '192.168.1.254',
                'usable_hosts' => 254,
                'total_hosts'  => 256,
                'ip_class'     => 'C',
                'is_private'   => true,
            ]],
            '/30 point-to-point' => ['10.0.0.1', 30, [
                'network'      => '10.0.0.0',
                'broadcast'    => '10.0.0.3',
                'host_min'     => '10.0.0.1',
                'host_max'     => '10.0.0.2',
                'usable_hosts' => 2,
                'is_private'   => true,
            ]],
            '/31 RFC 3021' => ['10.0.0.0', 31, [
                'network'      => '10.0.0.0',
                'broadcast'    => '10.0.0.1',
                'host_min'     => '10.0.0.0',
                'host_max'     => '10.0.0.1',
                'usable_hosts' => 2,
            ]],
            '/32 host singolo' => ['8.8.8.8', 32, [
                'network'      => '8.8.8.8',
                'broadcast'    => '8.8.8.8',
                'usable_hosts' => 1,
                'is_private'   => false,
                'ip_class'     => 'A',
            ]],
            '/16 pubblico classe B' => ['172.32.5.5', 16, [
                'network'      => '172.32.0.0',
                'broadcast'    => '172.32.255.255',
                'netmask'      => '255.255.0.0',
                'usable_hosts' => 65534,
                'ip_class'     => 'B',
                'is_private'   => false,
            ]],
        ];
    }
}
