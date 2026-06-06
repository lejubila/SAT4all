<?php

namespace Tests\Feature\Tools;

use App\Tools\OsiModel\OsiModel;
use Tests\TestCase;

class OsiModelTest extends TestCase
{
    public function test_shows_osi_model_page(): void
    {
        $this->get(route('tools.osi-model.index'))
            ->assertOk()
            ->assertSee(__('tools.osi_model.title'));
    }

    public function test_page_shows_all_seven_layers(): void
    {
        $response = $this->get(route('tools.osi-model.index'))->assertOk();

        foreach (range(1, 7) as $n) {
            $response->assertSee((string) $n);
        }
    }

    public function test_page_shows_layer_names(): void
    {
        $response = $this->get(route('tools.osi-model.index'))->assertOk();

        $response->assertSee(__('tools.osi_model.layer_7_application_name'));
        $response->assertSee(__('tools.osi_model.layer_4_transport_name'));
        $response->assertSee(__('tools.osi_model.layer_1_physical_name'));
    }

    public function test_page_shows_key_protocols(): void
    {
        $this->get(route('tools.osi-model.index'))
            ->assertOk()
            ->assertSee('TCP')
            ->assertSee('UDP')
            ->assertSee('IPv4')
            ->assertSee('HTTP');
    }

    public function test_layers_returns_seven_entries(): void
    {
        $this->assertCount(7, OsiModel::layers());
    }

    public function test_layers_are_ordered_from_seven_to_one(): void
    {
        $numbers = array_column(OsiModel::layers(), 'number');
        $this->assertSame([7, 6, 5, 4, 3, 2, 1], $numbers);
    }

    public function test_every_layer_has_required_keys(): void
    {
        foreach (OsiModel::layers() as $layer) {
            $this->assertArrayHasKey('number', $layer);
            $this->assertArrayHasKey('key', $layer);
            $this->assertArrayHasKey('pdu', $layer);
            $this->assertArrayHasKey('protocols', $layer);
            $this->assertArrayHasKey('devices', $layer);
            $this->assertArrayHasKey('color', $layer);
            $this->assertNotEmpty($layer['protocols']);
            $this->assertNotEmpty($layer['devices']);
        }
    }

    public function test_color_classes_covers_all_layer_colors(): void
    {
        $colorClasses = OsiModel::colorClasses();

        foreach (OsiModel::layers() as $layer) {
            $this->assertArrayHasKey($layer['color'], $colorClasses, "Color '{$layer['color']}' missing from colorClasses");
        }
    }
}
