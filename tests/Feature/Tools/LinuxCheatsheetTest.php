<?php

namespace Tests\Feature\Tools;

use App\Tools\LinuxCheatsheet\LinuxCheatsheet;
use Tests\TestCase;

class LinuxCheatsheetTest extends TestCase
{
    public function test_shows_cheatsheet_page(): void
    {
        $this->get(route('tools.linux-cheatsheet.index'))
            ->assertOk()
            ->assertSee(__('tools.linux_cheatsheet.title'));
    }

    public function test_page_contains_category_labels_in_json(): void
    {
        $response = $this->get(route('tools.linux-cheatsheet.index'))->assertOk();

        foreach (array_keys(LinuxCheatsheet::categories()) as $key) {
            $label = __("tools.linux_cheatsheet.cat_{$key}");
            // Il label è nel JSON inline passato ad Alpine (con escape unicode)
            $response->assertSee($label, false);
        }
    }

    public function test_page_contains_key_commands_in_json(): void
    {
        $response = $this->get(route('tools.linux-cheatsheet.index'))->assertOk();

        // I comandi sono nel JSON inline — assertSee senza escape HTML
        $response->assertSee('ps aux',       false);
        $response->assertSee('ip addr show', false);
        $response->assertSee('tar -czf',     false);
    }

    public function test_categories_returns_eight_groups(): void
    {
        $this->assertCount(8, LinuxCheatsheet::categories());
    }

    public function test_every_entry_has_required_keys(): void
    {
        foreach (LinuxCheatsheet::categories() as $catKey => $commands) {
            $this->assertNotEmpty($commands, "Category '{$catKey}' is empty");
            foreach ($commands as $entry) {
                $this->assertArrayHasKey('cmd',     $entry, "Missing 'cmd' in {$catKey}");
                $this->assertArrayHasKey('desc',    $entry, "Missing 'desc' in {$catKey}");
                $this->assertArrayHasKey('example', $entry, "Missing 'example' in {$catKey}");
                $this->assertNotEmpty($entry['cmd']);
                $this->assertNotEmpty($entry['desc']);
                $this->assertNotEmpty($entry['example']);
            }
        }
    }

    public function test_total_commands_count(): void
    {
        $total = array_sum(array_map('count', LinuxCheatsheet::categories()));
        $this->assertGreaterThanOrEqual(80, $total);
    }
}
