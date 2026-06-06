<?php

namespace Tests\Feature\Tools;

use App\Tools\BandwidthCalculator\BandwidthCalculator;
use Tests\TestCase;

class BandwidthCalculatorTest extends TestCase
{
    public function test_shows_page(): void
    {
        $this->get(route('tools.bandwidth-calculator.index'))
            ->assertOk()
            ->assertSee(__('tools.bandwidth_calculator.title'));
    }

    public function test_transfer_time_mode_returns_result(): void
    {
        $this->post(route('tools.bandwidth-calculator.calculate'), [
            'mode'       => 'time',
            'size_value' => '1',
            'size_unit'  => 'GB',
            'bw_value'   => '100',
            'bw_unit'    => 'Mbps',
            'overhead'   => '0',
        ])->assertOk()->assertSee(__('tools.bandwidth_calculator.result_time'));
    }

    public function test_size_mode_returns_result(): void
    {
        $this->post(route('tools.bandwidth-calculator.calculate'), [
            'mode'       => 'size',
            'bw_value'   => '100',
            'bw_unit'    => 'Mbps',
            'time_value' => '1',
            'time_unit'  => 'h',
            'overhead'   => '0',
        ])->assertOk()->assertSee(__('tools.bandwidth_calculator.result_size'));
    }

    public function test_bandwidth_mode_returns_result(): void
    {
        $this->post(route('tools.bandwidth-calculator.calculate'), [
            'mode'       => 'bandwidth',
            'size_value' => '10',
            'size_unit'  => 'GB',
            'time_value' => '1',
            'time_unit'  => 'h',
            'overhead'   => '0',
        ])->assertOk()->assertSee(__('tools.bandwidth_calculator.result_bandwidth'));
    }

    public function test_zero_bandwidth_returns_error(): void
    {
        $this->post(route('tools.bandwidth-calculator.calculate'), [
            'mode'       => 'time',
            'size_value' => '1',
            'size_unit'  => 'GB',
            'bw_value'   => '0',
            'bw_unit'    => 'Mbps',
            'overhead'   => '0',
        ])->assertOk()->assertSee(__('tools.bandwidth_calculator.error_positive'));
    }

    public function test_invalid_mode_fails_validation(): void
    {
        $this->post(route('tools.bandwidth-calculator.calculate'), [
            'mode'       => 'invalid',
            'size_value' => '1',
            'size_unit'  => 'GB',
        ])->assertOk()->assertSee(__('tools.bandwidth_calculator.error_validation'));
    }

    public function test_class_transfer_time_1gb_100mbps(): void
    {
        $c = new BandwidthCalculator();
        $r = $c->calculate('time', 1, 'GB', 100, 'Mbps', 0, 's', 0);
        $this->assertTrue($r['valid']);
        // 1 GB = 8,000,000,000 bits / 100,000,000 bps = 80 s
        $this->assertEqualsWithDelta(80.0, $r['result']['seconds'], 0.001);
    }

    public function test_class_transferable_size_100mbps_1h(): void
    {
        $c = new BandwidthCalculator();
        $r = $c->calculate('size', 0, 'B', 100, 'Mbps', 1, 'h', 0);
        $this->assertTrue($r['valid']);
        // 100,000,000 bps / 8 * 3600 s = 45,000,000,000 bytes = 45 GB
        $this->assertEqualsWithDelta(45_000_000_000, $r['result']['bytes'], 1);
    }

    public function test_class_required_bandwidth_10gb_1h(): void
    {
        $c = new BandwidthCalculator();
        $r = $c->calculate('bandwidth', 10, 'GB', 0, 'bps', 1, 'h', 0);
        $this->assertTrue($r['valid']);
        // 10 GB = 80,000,000,000 bits / 3600 s ≈ 22,222,222 bps ≈ 22.22 Mbps
        $this->assertEqualsWithDelta(22_222_222.22, $r['result']['bps'], 1);
    }

    public function test_class_overhead_increases_time(): void
    {
        $c    = new BandwidthCalculator();
        $base = $c->calculate('time', 1, 'GB', 100, 'Mbps', 0, 's', 0);
        $over = $c->calculate('time', 1, 'GB', 100, 'Mbps', 0, 's', 10);
        $this->assertGreaterThan($base['result']['seconds'], $over['result']['seconds']);
    }

    public function test_class_fmt_time_displays_correctly(): void
    {
        $c = new BandwidthCalculator();
        $this->assertSame('45.00 s', $c->fmtTime(45));
        $this->assertSame('1 min 20 s', $c->fmtTime(80));
        $this->assertSame('1 h 0 min', $c->fmtTime(3600));
    }

    public function test_class_fmt_size_scales_correctly(): void
    {
        $c = new BandwidthCalculator();
        $this->assertStringContainsString('GB', $c->fmtSize(1_000_000_000));
        $this->assertStringContainsString('MB', $c->fmtSize(1_000_000));
    }
}
