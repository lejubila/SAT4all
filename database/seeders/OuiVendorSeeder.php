<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OuiVendorSeeder extends Seeder
{
    // IEEE MA-L public OUI CSV
    private const IEEE_CSV_URL = 'https://standards-oui.ieee.org/oui/oui.csv';

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        DB::table('oui_vendors')->truncate();

        $this->command->info('Downloading IEEE OUI database…');

        $rows = $this->fetchFromIeee();

        if (empty($rows)) {
            $this->command->warn('IEEE download failed — seeding from embedded fallback.');
            $rows = $this->fallbackData();
        }

        $this->command->info('Inserting ' . count($rows) . ' OUI entries…');

        foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
            DB::table('oui_vendors')->insertOrIgnore($chunk);
        }

        $this->command->info('Done: ' . DB::table('oui_vendors')->count() . ' OUI entries loaded.');
    }

    private function fetchFromIeee(): array
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout'    => 30,
                'user_agent' => 'SysAdminToolkit/1.0',
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $handle = @fopen(self::IEEE_CSV_URL, 'r', false, $ctx);
        if ($handle === false) {
            return [];
        }

        $rows    = [];
        $headers = null;

        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = $line; // skip header row
                continue;
            }

            // CSV columns: Registry, Assignment, Organization Name, Organization Address
            if (count($line) < 3) {
                continue;
            }

            $prefix = strtoupper(trim($line[1]));
            $vendor = trim($line[2]);

            if (strlen($prefix) !== 6 || ! ctype_xdigit($prefix) || $vendor === '') {
                continue;
            }

            $rows[] = ['prefix' => $prefix, 'vendor' => $vendor];
        }

        fclose($handle);

        return $rows;
    }

    // Fallback: the embedded list from MacLookup (covers most common vendors)
    private function fallbackData(): array
    {
        $oui = \App\Tools\MacLookup\MacLookup::ouiArray();
        return array_map(
            fn ($prefix, $vendor) => ['prefix' => $prefix, 'vendor' => $vendor],
            array_keys($oui),
            array_values($oui)
        );
    }
}
