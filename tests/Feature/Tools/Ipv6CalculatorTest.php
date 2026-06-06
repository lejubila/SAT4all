<?php

namespace Tests\Feature\Tools;

use App\Tools\Ipv6Calculator\Ipv6Calculator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class Ipv6CalculatorTest extends TestCase
{
    public function test_shows_calculator_page(): void
    {
        $this->get(route('tools.ipv6-calculator.index'))
            ->assertOk()
            ->assertSee(__('tools.ipv6_calculator.title'))
            ->assertSee('/64')
            ->assertSee('/128');
    }

    public function test_calculates_subnet_correctly(): void
    {
        $this->post(route('tools.ipv6-calculator.calculate'), [
            'address' => '2001:db8::1',
            'prefix'  => 64,
        ])
            ->assertOk()
            ->assertSee('2001:db8::/64')                          // rete
            ->assertSee('2001:db8::ffff:ffff:ffff:ffff')          // ultimo indirizzo
            ->assertSee('2001:0db8:0000:0000:0000:0000:0000:0001') // forma estesa
            ->assertSee('18446744073709551616');                  // 2^64 indirizzi
    }

    public function test_invalid_address_returns_validation_error(): void
    {
        $this->post(route('tools.ipv6-calculator.calculate'), [
            'address' => '192.168.1.1',   // IPv4, non IPv6
            'prefix'  => 64,
        ])
            ->assertOk()
            ->assertSee(__('tools.ipv6_calculator.error_address_invalid'));
    }

    public function test_prefix_out_of_range_returns_validation_error(): void
    {
        $this->post(route('tools.ipv6-calculator.calculate'), [
            'address' => '2001:db8::1',
            'prefix'  => 200,
        ])
            ->assertOk()
            ->assertSee(__('tools.ipv6_calculator.error_prefix_range'));
    }

    /**
     * @param  array<string, mixed>  $expected
     */
    #[DataProvider('ipv6Provider')]
    public function test_logic_class_computes_expected_values(string $address, int $prefix, array $expected): void
    {
        $result = (new Ipv6Calculator(['address' => $address, 'prefix' => $prefix]))->calculate();

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $result[$key], "Campo {$key}");
        }
    }

    /**
     * @return array<string, array{0: string, 1: int, 2: array<string, mixed>}>
     */
    public static function ipv6Provider(): array
    {
        return [
            'documentazione /64' => ['2001:db8::1', 64, [
                'network'         => '2001:db8::',
                'first_address'   => '2001:db8::',
                'last_address'    => '2001:db8::ffff:ffff:ffff:ffff',
                'expanded'        => '2001:0db8:0000:0000:0000:0000:0000:0001',
                'total_addresses' => '18446744073709551616',
                'type'            => 'global_unicast',
            ]],
            'loopback /128' => ['::1', 128, [
                'network'         => '::1',
                'first_address'   => '::1',
                'last_address'    => '::1',
                'total_addresses' => '1',
                'type'            => 'loopback',
            ]],
            'link-local /10' => ['fe80::1', 10, [
                'network' => 'fe80::',
                'type'    => 'link_local',
            ]],
            'unique-local /7' => ['fc00::1', 7, [
                'type' => 'unique_local',
            ]],
            'multicast /16' => ['ff02::1', 16, [
                'type' => 'multicast',
            ]],
            'unspecified /128' => ['::', 128, [
                'type' => 'unspecified',
            ]],
        ];
    }
}
