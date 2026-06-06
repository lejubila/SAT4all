<?php

namespace Tests\Feature\Tools;

use App\Tools\CableColors\CableColors;
use Tests\TestCase;

class CableColorsTest extends TestCase
{
    public function test_shows_cable_colors_page(): void
    {
        $this->get(route('tools.cable-colors.index'))
            ->assertOk()
            ->assertSee(__('tools.cable_colors.title'));
    }

    public function test_page_shows_both_standards(): void
    {
        $this->get(route('tools.cable-colors.index'))
            ->assertOk()
            ->assertSee('T568A')
            ->assertSee('T568B');
    }

    public function test_page_shows_color_names(): void
    {
        $this->get(route('tools.cable-colors.index'))
            ->assertOk()
            ->assertSee(__('tools.cable_colors.color_white_orange'))
            ->assertSee(__('tools.cable_colors.color_green'));
    }

    public function test_standards_returns_two_keys(): void
    {
        $standards = CableColors::standards();
        $this->assertArrayHasKey('t568a', $standards);
        $this->assertArrayHasKey('t568b', $standards);
    }

    public function test_each_standard_has_eight_pins(): void
    {
        foreach (CableColors::standards() as $key => $pins) {
            $this->assertCount(8, $pins, "{$key} should have 8 pins");
        }
    }

    public function test_pin_numbers_are_one_to_eight(): void
    {
        foreach (CableColors::standards() as $pins) {
            $numbers = array_column($pins, 'pin');
            $this->assertSame(range(1, 8), $numbers);
        }
    }

    public function test_t568a_and_t568b_differ_on_correct_pins(): void
    {
        $standards = CableColors::standards();
        $diffPins  = CableColors::differingPins();

        foreach (range(1, 8) as $pin) {
            $a = $standards['t568a'][$pin - 1];
            $b = $standards['t568b'][$pin - 1];

            if (in_array($pin, $diffPins, true)) {
                $this->assertNotSame($a['color_key'], $b['color_key'],
                    "Pin {$pin} should differ between T568A and T568B");
            } else {
                $this->assertSame($a['color_key'], $b['color_key'],
                    "Pin {$pin} should be equal in T568A and T568B");
            }
        }
    }

    public function test_differing_pins_are_1_2_3_6(): void
    {
        $this->assertSame([1, 2, 3, 6], CableColors::differingPins());
    }

    public function test_pairs_returns_four_entries(): void
    {
        $this->assertCount(4, CableColors::pairs());
    }

    public function test_every_pin_entry_has_required_keys(): void
    {
        $required = ['pin', 'name_key', 'stripe', 'swatch', 'text', 'pair', 'func_fast', 'func_gig', 'color_key'];
        foreach (CableColors::standards() as $pins) {
            foreach ($pins as $pin) {
                foreach ($required as $key) {
                    $this->assertArrayHasKey($key, $pin, "Missing '{$key}' on pin {$pin['pin']}");
                }
            }
        }
    }
}
