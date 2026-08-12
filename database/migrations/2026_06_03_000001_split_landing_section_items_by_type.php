<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move list-type landing-section content from the shared `items`
     * key to a distinct per-type key.
     *
     * Why
     * ---
     * The landing-page builder used four Repeater::make('content.items')
     * components (features / testimonials / gallery / faq) that all
     * bound to the SAME `content.items` state path.  Duplicate state
     * paths in one Filament schema create ambiguous Livewire bindings —
     * "add item" misfired ("some category won't add object like
     * testimonial").  Each repeater now owns a distinct key; this
     * migration moves any EXISTING saved data over so it keeps
     * rendering + edits cleanly:
     *
     *   features     items -> feature_items
     *   testimonials items -> testimonials
     *   gallery      items -> gallery_items
     *   faq          items -> faqs
     *
     * The section blades read the new key with an `items` fallback, so
     * even un-migrated rows still RENDER — this migration is what makes
     * EDITING an old section load its data into the new repeater.
     *
     * Idempotent: rows already migrated (no `items` key) are skipped,
     * and an existing new-key value is never clobbered.
     */
    public function up(): void
    {
        if (! Schema::hasTable('landing_page_sections')) {
            return;
        }

        $map = [
            'features'     => 'feature_items',
            'testimonials' => 'testimonials',
            'gallery'      => 'gallery_items',
            'faq'          => 'faqs',
        ];

        DB::table('landing_page_sections')
            ->whereIn('type', array_keys($map))
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($map): void {
                foreach ($rows as $row) {
                    $content = json_decode((string) ($row->content ?? ''), true);
                    if (! is_array($content) || ! array_key_exists('items', $content)) {
                        continue;
                    }
                    $newKey = $map[$row->type] ?? null;
                    if ($newKey === null) {
                        continue;
                    }
                    // Don't clobber a value that's already on the new key.
                    if (! array_key_exists($newKey, $content)) {
                        $content[$newKey] = $content['items'];
                    }
                    unset($content['items']);

                    DB::table('landing_page_sections')
                        ->where('id', $row->id)
                        ->update(['content' => json_encode($content)]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('landing_page_sections')) {
            return;
        }

        $map = [
            'features'     => 'feature_items',
            'testimonials' => 'testimonials',
            'gallery'      => 'gallery_items',
            'faq'          => 'faqs',
        ];

        DB::table('landing_page_sections')
            ->whereIn('type', array_keys($map))
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($map): void {
                foreach ($rows as $row) {
                    $content = json_decode((string) ($row->content ?? ''), true);
                    if (! is_array($content)) {
                        continue;
                    }
                    $newKey = $map[$row->type] ?? null;
                    if ($newKey === null || ! array_key_exists($newKey, $content)) {
                        continue;
                    }
                    if (! array_key_exists('items', $content)) {
                        $content['items'] = $content[$newKey];
                    }
                    unset($content[$newKey]);

                    DB::table('landing_page_sections')
                        ->where('id', $row->id)
                        ->update(['content' => json_encode($content)]);
                }
            });
    }
};
