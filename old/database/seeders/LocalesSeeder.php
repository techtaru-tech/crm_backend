<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

/**
 * Seeds the four shipped locales (English, Arabic, Spanish, Hindi)
 * when the locales table is empty.  On re-run it's a no-op because
 * each row is firstOrCreate'd by code — operator edits are never
 * clobbered.
 */
class LocalesSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['code' => 'en', 'name' => 'English',   'english_name' => 'English', 'flag' => '🇬🇧', 'rtl' => false, 'is_default' => true,  'sort_order' => 10],
            ['code' => 'ar', 'name' => 'العربية',   'english_name' => 'Arabic',  'flag' => '🇸🇦', 'rtl' => true,  'is_default' => false, 'sort_order' => 20],
            ['code' => 'es', 'name' => 'Español',   'english_name' => 'Spanish', 'flag' => '🇪🇸', 'rtl' => false, 'is_default' => false, 'sort_order' => 30],
            ['code' => 'hi', 'name' => 'हिन्दी',     'english_name' => 'Hindi',   'flag' => '🇮🇳', 'rtl' => false, 'is_default' => false, 'sort_order' => 40],
        ];

        foreach ($defaults as $row) {
            Locale::firstOrCreate(
                ['code' => $row['code']],
                array_merge($row, ['enabled' => true])
            );
        }
    }
}
