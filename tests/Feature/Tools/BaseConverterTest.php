<?php

namespace Tests\Feature\Tools;

use App\Tools\BaseConverter\BaseConverter;
use Tests\TestCase;

class BaseConverterTest extends TestCase
{
    public function test_shows_page(): void
    {
        $this->get(route('tools.base-converter.index'))
            ->assertOk()
            ->assertSee(__('tools.base_converter.title'));
    }

    public function test_empty_number_returns_idle(): void
    {
        $this->post(route('tools.base-converter.convert'), [
            'number'    => '',
            'from_base' => '10',
        ])->assertOk()->assertSee(__('tools.base_converter.idle'));
    }

    public function test_decimal_255_converts_correctly(): void
    {
        $this->post(route('tools.base-converter.convert'), [
            'number'    => '255',
            'from_base' => '10',
        ])->assertOk()
          ->assertSee('11111111')   // binary
          ->assertSee('FF')         // hex
          ->assertSee('377');       // octal
    }

    public function test_hex_ff_converts_to_decimal_255(): void
    {
        $this->post(route('tools.base-converter.convert'), [
            'number'    => 'ff',
            'from_base' => '16',
        ])->assertOk()->assertSee('255');
    }

    public function test_binary_1010_converts_to_decimal_10(): void
    {
        $this->post(route('tools.base-converter.convert'), [
            'number'    => '1010',
            'from_base' => '2',
        ])->assertOk()->assertSee('10')->assertSee('A');
    }

    public function test_invalid_chars_returns_error(): void
    {
        $this->post(route('tools.base-converter.convert'), [
            'number'    => '29',  // 9 is invalid in octal
            'from_base' => '8',
        ])->assertOk()->assertSee(__('tools.base_converter.error_invalid_chars'));
    }

    public function test_missing_base_returns_error(): void
    {
        $this->post(route('tools.base-converter.convert'), [
            'number'    => '255',
            'from_base' => '',
        ])->assertOk()->assertSee(__('tools.base_converter.error_base_required'));
    }

    public function test_class_dec_to_all_bases(): void
    {
        $c = new BaseConverter();
        $r = $c->convert('255', 10);
        $this->assertTrue($r['valid']);
        $values = array_column($r['results'], 'value', 'label');
        $this->assertSame('11111111', $values['BIN']);
        $this->assertSame('377', $values['OCT']);
        $this->assertSame('255', $values['DEC']);
        $this->assertSame('FF', $values['HEX']);
    }

    public function test_class_hex_to_dec(): void
    {
        $c = new BaseConverter();
        $r = $c->convert('1a2b', 16);
        $this->assertTrue($r['valid']);
        $values = array_column($r['results'], 'value', 'label');
        $this->assertSame('6699', $values['DEC']);
    }

    public function test_class_binary_invalid(): void
    {
        $c = new BaseConverter();
        $r = $c->convert('1012', 2);
        $this->assertFalse($r['valid']);
        $this->assertSame('invalid_chars', $r['error']);
    }

    public function test_class_ascii_shown_for_printable(): void
    {
        $c = new BaseConverter();
        $r = $c->convert('65', 10);  // 'A'
        $this->assertTrue($r['valid']);
        $this->assertSame('A', $r['extras']['ascii']);
    }

    public function test_class_zero(): void
    {
        $c = new BaseConverter();
        $r = $c->convert('0', 10);
        $this->assertTrue($r['valid']);
        $values = array_column($r['results'], 'value', 'label');
        $this->assertSame('0', $values['BIN']);
        $this->assertSame('0', $values['HEX']);
    }
}
