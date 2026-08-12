<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds the eight stat-strip fields to the `landing` settings group
 * so a SuperAdmin can override the "19 / Lead Sources … 0 / Lock-in"
 * tiles rendered on marketing.landing-light.blade.php (or any future
 * variant that surfaces the same stat strip) without editing the
 * marketing.php lang file.
 *
 * Nulls on creation so existing installs keep showing the current
 * stock values (the translator-key fallback in the blade hands back
 * the original "19 Lead Sources" copy until an operator overrides).
 *
 * Stat 3's number is "∞" in the stock blade — also stored as a
 * nullable string here so the operator can replace the glyph if
 * they want a hard cap displayed.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('landing.stat1_number', null);
        $this->migrator->add('landing.stat1_label',  null);
        $this->migrator->add('landing.stat2_number', null);
        $this->migrator->add('landing.stat2_label',  null);
        $this->migrator->add('landing.stat3_number', null);
        $this->migrator->add('landing.stat3_label',  null);
        $this->migrator->add('landing.stat4_number', null);
        $this->migrator->add('landing.stat4_label',  null);
    }

    public function down(): void
    {
        $this->migrator->delete('landing.stat1_number');
        $this->migrator->delete('landing.stat1_label');
        $this->migrator->delete('landing.stat2_number');
        $this->migrator->delete('landing.stat2_label');
        $this->migrator->delete('landing.stat3_number');
        $this->migrator->delete('landing.stat3_label');
        $this->migrator->delete('landing.stat4_number');
        $this->migrator->delete('landing.stat4_label');
    }
};
