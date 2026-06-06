<?php

namespace Tests\Feature\Tools;

use App\Tools\RfcBrowser\RfcBrowser;
use Tests\TestCase;

class RfcBrowserTest extends TestCase
{
    public function test_shows_rfc_browser_page(): void
    {
        $this->get(route('tools.rfc-browser.index'))
            ->assertOk()
            ->assertSee(__('tools.rfc_browser.title'));
    }

    public function test_page_contains_search_input(): void
    {
        $this->get(route('tools.rfc-browser.index'))
            ->assertOk()
            ->assertSee(__('tools.rfc_browser.search_placeholder'));
    }

    public function test_page_shows_well_known_rfcs(): void
    {
        $this->get(route('tools.rfc-browser.index'))
            ->assertOk()
            ->assertSee('791')   // IPv4
            ->assertSee('793')   // TCP
            ->assertSee('8446'); // TLS 1.3
    }

    public function test_rfcs_returns_non_empty_array(): void
    {
        $rfcs = RfcBrowser::rfcs();
        $this->assertNotEmpty($rfcs);
        $this->assertGreaterThan(50, count($rfcs));
    }

    public function test_every_rfc_has_required_keys(): void
    {
        $required = ['number', 'title', 'year', 'status', 'category'];
        foreach (RfcBrowser::rfcs() as $rfc) {
            foreach ($required as $key) {
                $this->assertArrayHasKey($key, $rfc, "Missing '{$key}' on RFC {$rfc['number']}");
            }
        }
    }

    public function test_rfc_numbers_are_unique(): void
    {
        $numbers = array_column(RfcBrowser::rfcs(), 'number');
        $this->assertCount(count($numbers), array_unique($numbers), 'Duplicate RFC numbers found');
    }

    public function test_all_statuses_are_valid(): void
    {
        $validStatuses = RfcBrowser::statuses();
        foreach (RfcBrowser::rfcs() as $rfc) {
            $this->assertContains(
                $rfc['status'],
                $validStatuses,
                "RFC {$rfc['number']} has invalid status '{$rfc['status']}'"
            );
        }
    }

    public function test_all_categories_are_valid(): void
    {
        $validCategories = RfcBrowser::categories();
        foreach (RfcBrowser::rfcs() as $rfc) {
            $this->assertContains(
                $rfc['category'],
                $validCategories,
                "RFC {$rfc['number']} has invalid category '{$rfc['category']}'"
            );
        }
    }

    public function test_categories_returns_expected_list(): void
    {
        $cats = RfcBrowser::categories();
        foreach (['networking', 'routing', 'dns', 'email', 'web', 'security', 'management', 'reference'] as $expected) {
            $this->assertContains($expected, $cats);
        }
    }

    public function test_rfcs_include_each_category(): void
    {
        $rfcs = RfcBrowser::rfcs();
        foreach (RfcBrowser::categories() as $cat) {
            $found = array_filter($rfcs, fn ($r) => $r['category'] === $cat);
            $this->assertNotEmpty($found, "No RFC found in category '{$cat}'");
        }
    }

    public function test_rfc_years_are_plausible(): void
    {
        foreach (RfcBrowser::rfcs() as $rfc) {
            $this->assertGreaterThanOrEqual(1969, $rfc['year'], "RFC {$rfc['number']} has too-old year");
            $this->assertLessThanOrEqual(date('Y') + 1, $rfc['year'], "RFC {$rfc['number']} has future year");
        }
    }
}
