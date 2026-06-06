<?php

namespace Tests\Feature\Tools;

use App\Tools\Formatter\Formatter;
use Tests\TestCase;

class FormatterTest extends TestCase
{
    public function test_shows_page(): void
    {
        $this->get(route('tools.formatter.index'))
            ->assertOk()
            ->assertSee(__('tools.formatter.title'));
    }

    public function test_empty_input_returns_idle(): void
    {
        $this->post(route('tools.formatter.format'), [
            'input'  => '',
            'format' => 'auto',
            'indent' => 4,
        ])->assertOk()->assertSee(__('tools.formatter.idle'));
    }

    public function test_format_json_valid(): void
    {
        $this->post(route('tools.formatter.format'), [
            'input'  => '{"name":"test","value":42}',
            'format' => 'json',
            'indent' => 4,
        ])->assertOk()
          ->assertSee('"name"')
          ->assertSee('"test"');
    }

    public function test_format_xml_valid(): void
    {
        $this->post(route('tools.formatter.format'), [
            'input'  => '<root><child>hello</child></root>',
            'format' => 'xml',
            'indent' => 4,
        ])->assertOk()
          ->assertSee('child')
          ->assertSee('hello');
    }

    public function test_format_html_valid(): void
    {
        $this->post(route('tools.formatter.format'), [
            'input'  => '<div><p>Hello</p></div>',
            'format' => 'html',
            'indent' => 4,
        ])->assertOk()
          ->assertSee('Hello');
    }

    public function test_invalid_json_returns_error(): void
    {
        $this->post(route('tools.formatter.format'), [
            'input'  => '{invalid json}',
            'format' => 'json',
            'indent' => 4,
        ])->assertOk()->assertSee(__('tools.formatter.error_invalid', ['format' => 'JSON']));
    }

    public function test_auto_detects_json(): void
    {
        $f = new Formatter();
        $r = $f->format('{"x":1}', 'auto', 4);
        $this->assertTrue($r['valid']);
        $this->assertSame('json', $r['format']);
    }

    public function test_auto_detects_xml(): void
    {
        $f = new Formatter();
        $r = $f->format('<root><a>1</a></root>', 'auto', 4);
        $this->assertTrue($r['valid']);
        $this->assertSame('xml', $r['format']);
    }

    public function test_class_indent_2_spaces(): void
    {
        $f = new Formatter();
        $r = $f->format('{"a":{"b":1}}', 'json', 2);
        $this->assertTrue($r['valid']);
        // Level 1 must be 2 spaces, not 4
        $this->assertStringContainsString("\n  \"a\":", $r['output']);
        // Level 2 is 4 spaces (2×2) in 2-space mode
        $this->assertStringContainsString("\n    \"b\":", $r['output']);
    }

    public function test_class_too_large_returns_error(): void
    {
        $f = new Formatter();
        $r = $f->format(str_repeat('a', 200_001), 'json', 4);
        $this->assertFalse($r['valid'] ?? true);
        $this->assertSame('too_large', $r['error']);
    }

    public function test_class_json_output_has_line_count(): void
    {
        $f = new Formatter();
        $r = $f->format('{"a":1,"b":2}', 'json', 4);
        $this->assertTrue($r['valid']);
        $this->assertGreaterThan(1, $r['lines']);
    }

    public function test_unknown_format_auto_returns_error(): void
    {
        $f = new Formatter();
        $r = $f->format('just plain text', 'auto', 4);
        $this->assertFalse($r['valid'] ?? true);
        $this->assertSame('unknown_format', $r['error']);
    }
}
