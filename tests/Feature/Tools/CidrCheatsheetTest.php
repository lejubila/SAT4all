<?php

namespace Tests\Feature\Tools;

use App\Tools\CidrCheatsheet\CidrCheatsheet;
use Tests\TestCase;

class CidrCheatsheetTest extends TestCase
{
    public function test_shows_cheatsheet_page(): void
    {
        $this->get(route('tools.cidr-cheatsheet.index'))
            ->assertOk()
            ->assertSee(__('tools.cidr_cheatsheet.title'))
            ->assertSee('/24')
            ->assertSee('255.255.255.0');
    }

    public function test_page_shows_all_32_prefixes(): void
    {
        $response = $this->get(route('tools.cidr-cheatsheet.index'))->assertOk();

        foreach ([0, 8, 16, 24, 32] as $cidr) {
            $response->assertSee('/'.$cidr);
        }
    }

    public function test_rows_count_is_33(): void
    {
        $this->assertCount(33, CidrCheatsheet::rows());
    }

    public function test_known_values_slash24(): void
    {
        $row = CidrCheatsheet::rows()[24];

        $this->assertSame(24,              $row['cidr']);
        $this->assertSame('255.255.255.0', $row['netmask']);
        $this->assertSame('0.0.0.255',     $row['wildcard']);
        $this->assertSame('256',           $row['total']);
        $this->assertSame('254',           $row['usable']);
        $this->assertSame('classe C',      $row['note']);
    }

    public function test_known_values_slash32(): void
    {
        $row = CidrCheatsheet::rows()[32];

        $this->assertSame('255.255.255.255', $row['netmask']);
        $this->assertSame('0.0.0.0',         $row['wildcard']);
        $this->assertSame('1',               $row['total']);
        $this->assertSame('1',               $row['usable']);
    }

    public function test_known_values_slash31(): void
    {
        $row = CidrCheatsheet::rows()[31];

        $this->assertSame('2', $row['total']);
        $this->assertSame('2', $row['usable']);
        $this->assertStringContainsString('RFC 3021', $row['note']);
    }

    public function test_known_values_slash0(): void
    {
        $row = CidrCheatsheet::rows()[0];

        $this->assertSame('0.0.0.0',         $row['netmask']);
        $this->assertSame('255.255.255.255',  $row['wildcard']);
        $this->assertStringContainsString('default route', $row['note']);
    }
}
