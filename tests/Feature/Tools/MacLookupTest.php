<?php

namespace Tests\Feature\Tools;

use App\Tools\MacLookup\MacLookup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MacLookupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed a minimal OUI dataset for tests
        DB::table('oui_vendors')->insertOrIgnore([
            ['prefix' => '005056', 'vendor' => 'VMware Inc.'],
            ['prefix' => '000C29', 'vendor' => 'VMware Inc.'],
            ['prefix' => '000569', 'vendor' => 'VMware Inc.'],
            ['prefix' => 'B827EB', 'vendor' => 'Raspberry Pi Foundation'],
            ['prefix' => '00000C', 'vendor' => 'Cisco Systems'],
        ]);
    }

    public function test_shows_mac_lookup_page(): void
    {
        $this->get(route('tools.mac-lookup.index'))
            ->assertOk()
            ->assertSee(__('tools.mac_lookup.title'));
    }

    public function test_shows_empty_state_on_load(): void
    {
        $this->get(route('tools.mac-lookup.index'))
            ->assertOk()
            ->assertSee(__('tools.mac_lookup.empty'));
    }

    public function test_empty_mac_returns_error(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => ''])
            ->assertOk()
            ->assertSee(__('tools.mac_lookup.error_required'));
    }

    public function test_invalid_mac_returns_error(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => 'ZZ:ZZ:ZZ:ZZ:ZZ:ZZ'])
            ->assertOk()
            ->assertSee(__('tools.mac_lookup.error_invalid'));
    }

    public function test_known_vendor_is_found(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => '00:50:56:AB:CD:EF'])
            ->assertOk()
            ->assertSee('VMware');
    }

    public function test_unknown_vendor_shows_not_found(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => '02:00:00:00:00:01'])
            ->assertOk()
            ->assertSee(__('tools.mac_lookup.vendor_unknown'));
    }

    public function test_normalize_accepts_colon_format(): void
    {
        $this->assertSame('AABBCCDDEEFF', MacLookup::normalize('AA:BB:CC:DD:EE:FF'));
    }

    public function test_normalize_accepts_dash_format(): void
    {
        $this->assertSame('AABBCCDDEEFF', MacLookup::normalize('AA-BB-CC-DD-EE-FF'));
    }

    public function test_normalize_accepts_plain_format(): void
    {
        $this->assertSame('AABBCCDDEEFF', MacLookup::normalize('AABBCCDDEEFF'));
    }

    public function test_normalize_accepts_dot_format(): void
    {
        $this->assertSame('AABBCCDDEEFF', MacLookup::normalize('AABB.CCDD.EEFF'));
    }

    public function test_normalize_accepts_oui_only_colon(): void
    {
        $this->assertSame('005056', MacLookup::normalize('00:50:56'));
    }

    public function test_normalize_accepts_oui_only_plain(): void
    {
        $this->assertSame('005056', MacLookup::normalize('005056'));
    }

    public function test_normalize_rejects_invalid_mac(): void
    {
        $this->assertNull(MacLookup::normalize('ZZ:ZZ:ZZ:ZZ:ZZ:ZZ'));
        $this->assertNull(MacLookup::normalize('00:11:22:33:44'));
        $this->assertNull(MacLookup::normalize(''));
    }

    public function test_format_returns_all_keys(): void
    {
        $fmt = MacLookup::format('AABBCCDDEEFF');
        $this->assertArrayHasKey('colon', $fmt);
        $this->assertArrayHasKey('dash',  $fmt);
        $this->assertArrayHasKey('dot',   $fmt);
        $this->assertArrayHasKey('plain', $fmt);
        $this->assertSame('AA:BB:CC:DD:EE:FF', $fmt['colon']);
        $this->assertSame('AA-BB-CC-DD-EE-FF', $fmt['dash']);
        $this->assertSame('AABB.CCDD.EEFF',    $fmt['dot']);
    }

    public function test_multicast_bit_detected(): void
    {
        $result = MacLookup::lookup('01:00:5E:00:00:01');
        $this->assertTrue($result['multicast']);
    }

    public function test_unicast_bit_detected(): void
    {
        $result = MacLookup::lookup('00:50:56:00:00:01');
        $this->assertFalse($result['multicast']);
    }

    public function test_locally_administered_bit_detected(): void
    {
        $result = MacLookup::lookup('02:00:00:00:00:01');
        $this->assertTrue($result['locally_administered']);
    }

    public function test_globally_administered_bit_detected(): void
    {
        $result = MacLookup::lookup('00:50:56:00:00:01');
        $this->assertFalse($result['locally_administered']);
    }

    public function test_oui_only_lookup_finds_vendor(): void
    {
        $result = MacLookup::lookup('00:50:56');
        $this->assertTrue($result['found']);
        $this->assertTrue($result['oui_only']);
        $this->assertArrayNotHasKey('nic', $result);
        $this->assertArrayNotHasKey('format', $result);
    }

    public function test_oui_only_via_http(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => '00:50:56'])
            ->assertOk()
            ->assertSee('VMware')
            ->assertSee(__('tools.mac_lookup.oui_only_badge'));
    }

    public function test_full_mac_lookup_has_nic_and_formats(): void
    {
        $result = MacLookup::lookup('00:50:56:AB:CD:EF');
        $this->assertFalse($result['oui_only']);
        $this->assertArrayHasKey('nic', $result);
        $this->assertArrayHasKey('format', $result);
    }

    public function test_db_has_seeded_entries(): void
    {
        $this->assertGreaterThan(0, DB::table('oui_vendors')->count());
        $vendor = DB::table('oui_vendors')->where('prefix', '005056')->value('vendor');
        $this->assertSame('VMware Inc.', $vendor);
    }

    // ── Virtual context detection ────────────────────────────────────────────

    public function test_vmware_oui_detected_as_virtual(): void
    {
        $result = MacLookup::lookup('00:0C:29:AB:CD:EF');
        $this->assertNotNull($result['virtual']);
        $this->assertSame('vmware', $result['virtual']['type']);
        $this->assertTrue($result['virtual']['certain']);
    }

    public function test_vmware_esxi_range_detected(): void
    {
        // 00:50:56:40:xx:xx → fourth octet 0x40 ≥ 0x40 → ESXi
        $result = MacLookup::lookup('00:50:56:40:AB:CD');
        $this->assertSame('vmware_esxi', $result['virtual']['type']);

        // 00:50:56:3F:xx:xx → fourth octet 0x3F < 0x40 → Workstation/Fusion
        $result2 = MacLookup::lookup('00:50:56:3F:AB:CD');
        $this->assertSame('vmware', $result2['virtual']['type']);
    }

    public function test_virtualbox_oui_detected(): void
    {
        $result = MacLookup::lookup('08:00:27:AB:CD:EF');
        $this->assertNotNull($result['virtual']);
        $this->assertSame('virtualbox', $result['virtual']['type']);
    }

    public function test_hyper_v_oui_detected(): void
    {
        $result = MacLookup::lookup('00:15:5D:AB:CD:EF');
        $this->assertSame('hyper_v', $result['virtual']['type']);
    }

    public function test_xen_oui_detected(): void
    {
        $result = MacLookup::lookup('00:16:3E:AB:CD:EF');
        $this->assertSame('xen', $result['virtual']['type']);
    }

    public function test_qemu_kvm_prefix_detected(): void
    {
        $result = MacLookup::lookup('52:54:00:AB:CD:EF');
        $this->assertSame('qemu_kvm', $result['virtual']['type']);
        $this->assertTrue($result['virtual']['certain']);
    }

    public function test_docker_prefix_detected(): void
    {
        $result = MacLookup::lookup('02:42:AC:11:00:02');
        $this->assertSame('docker', $result['virtual']['type']);
    }

    public function test_la_generic_for_unknown_local_mac(): void
    {
        // 02:xx:xx:xx:xx:xx that is NOT a known virtual prefix
        $result = MacLookup::lookup('02:11:22:33:44:55');
        $this->assertSame('la_generic', $result['virtual']['type']);
        $this->assertFalse($result['virtual']['certain']);
    }

    public function test_physical_mac_has_no_virtual_context(): void
    {
        $result = MacLookup::lookup('B8:27:EB:AB:CD:EF');
        $this->assertNull($result['virtual']);
    }

    public function test_virtual_context_section_shown_in_http_response(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => '00:0C:29:AB:CD:EF'])
            ->assertOk()
            ->assertSee(__('tools.mac_lookup.virtual_section'))
            ->assertSee(__('tools.mac_lookup.virtual_badge_certain'));
    }

    public function test_qemu_virtual_section_shown_in_http_response(): void
    {
        $this->post(route('tools.mac-lookup.lookup'), ['mac' => '52:54:00:12:34:56'])
            ->assertOk()
            ->assertSee('QEMU');
    }
}
