<?php

namespace Tests\Feature\Tools;

use App\Tools\HttpStatusCodes\HttpStatusCodes;
use Tests\TestCase;

class HttpStatusCodesTest extends TestCase
{
    public function test_shows_page(): void
    {
        $this->get(route('tools.http-status-codes.index'))
            ->assertOk()
            ->assertSee(__('tools.http_status_codes.title'));
    }

    public function test_page_contains_well_known_codes(): void
    {
        $this->get(route('tools.http-status-codes.index'))
            ->assertOk()
            ->assertSee('200')
            ->assertSee('404')
            ->assertSee('503');
    }

    public function test_codes_returns_non_empty_array(): void
    {
        $this->assertNotEmpty(HttpStatusCodes::codes());
    }

    public function test_all_five_categories_are_present(): void
    {
        $categories = array_unique(array_column(HttpStatusCodes::codes(), 'category'));
        foreach (['1xx', '2xx', '3xx', '4xx', '5xx'] as $cat) {
            $this->assertContains($cat, $categories, "Category {$cat} missing");
        }
    }

    public function test_every_code_has_required_keys(): void
    {
        foreach (HttpStatusCodes::codes() as $c) {
            foreach (['code', 'name', 'category', 'rfc', 'desc'] as $key) {
                $this->assertArrayHasKey($key, $c, "Missing '{$key}' on code {$c['code']}");
            }
        }
    }

    public function test_code_numbers_are_unique(): void
    {
        $numbers = array_column(HttpStatusCodes::codes(), 'code');
        $this->assertCount(count($numbers), array_unique($numbers), 'Duplicate HTTP codes found');
    }

    public function test_codes_are_in_valid_range(): void
    {
        foreach (HttpStatusCodes::codes() as $c) {
            $this->assertGreaterThanOrEqual(100, $c['code']);
            $this->assertLessThanOrEqual(599, $c['code']);
        }
    }

    public function test_categories_match_code_prefix(): void
    {
        foreach (HttpStatusCodes::codes() as $c) {
            $expected = (string) intdiv($c['code'], 100) . 'xx';
            $this->assertSame($expected, $c['category'], "Code {$c['code']} has wrong category");
        }
    }
}
