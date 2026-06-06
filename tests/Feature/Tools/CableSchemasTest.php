<?php

namespace Tests\Feature\Tools;

use App\Tools\CableSchemas\CableSchemas;
use Tests\TestCase;

class CableSchemasTest extends TestCase
{
    public function test_shows_cable_schemas_page(): void
    {
        $this->get(route('tools.cable-schemas.index'))
            ->assertOk()
            ->assertSee(__('tools.cable_schemas.title'))
            ->assertSee('T568A')
            ->assertSee('T568B');
    }

    public function test_page_shows_all_cable_types(): void
    {
        $this->get(route('tools.cable-schemas.index'))
            ->assertOk()
            ->assertSee(__('tools.cable_schemas.cable_straight'))
            ->assertSee(__('tools.cable_schemas.cable_crossover'))
            ->assertSee(__('tools.cable_schemas.cable_rollover'));
    }

    public function test_page_shows_use_cases(): void
    {
        $this->get(route('tools.cable-schemas.index'))
            ->assertSee(__('tools.cable_schemas.cable_straight_use'))
            ->assertSee(__('tools.cable_schemas.note_mdix'));
    }

    public function test_standards_have_eight_pins_each(): void
    {
        foreach (CableSchemas::standards() as $name => $pins) {
            $this->assertCount(8, $pins, "Standard {$name} deve avere 8 pin");

            foreach ($pins as $pin) {
                $this->assertArrayHasKey('label', $pin);
                $this->assertArrayHasKey('tw', $pin);
            }
        }
    }

    public function test_cables_map_has_eight_entries(): void
    {
        foreach (CableSchemas::cables() as $type => $cable) {
            $this->assertCount(8, $cable['map'], "Cavo {$type} deve mappare 8 pin");
        }
    }

    public function test_straight_cable_is_pin_to_pin(): void
    {
        $cable = CableSchemas::cables()['straight'];

        foreach ($cable['map'] as $idx => $dest) {
            $this->assertSame($idx + 1, $dest, "Pin ".($idx + 1)." deve mappare su se stesso nel cavo dritto");
        }
    }

    public function test_rollover_cable_is_reversed(): void
    {
        $cable = CableSchemas::cables()['rollover'];

        $this->assertSame([8, 7, 6, 5, 4, 3, 2, 1], $cable['map']);
    }

    public function test_crossover_uses_different_standards_on_each_end(): void
    {
        $cable = CableSchemas::cables()['crossover'];

        $this->assertNotSame($cable['standard_a'], $cable['standard_b']);
    }
}
