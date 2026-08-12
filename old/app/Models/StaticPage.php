<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * SuperAdmin-managed marketing page (About, Terms, Privacy, Contact…).
 *
 * - Rendered at /pages/{slug} by PublicStaticPageController.
 * - NOT tenant-scoped: these are SaaS-level content.
 * - Surfaced in the marketing navbar / footer via show_in_nav /
 *   show_in_footer flags; ordering controlled by nav_order.
 * - `published` + `published_at` gate visibility to the public.
 */
class StaticPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'excerpt',
        'content_html',
        'translations',
        'show_in_nav',
        'show_in_footer',
        'nav_order',
        'published',
        'published_at',
        'updated_by',
    ];

    protected $casts = [
        'translations'   => 'array',
        'show_in_nav'    => 'boolean',
        'show_in_footer' => 'boolean',
        'nav_order'      => 'integer',
        'published'      => 'boolean',
        'published_at'   => 'datetime',
    ];

    /**
     * Resolve a translatable field for the current (or provided) locale.
     * English always reads from the primary scalar column.  Other
     * locales look in the `translations` JSON map and fall back to
     * English when a value is missing or empty — so a half-translated
     * page still renders a complete view.
     */
    public function t(string $field, ?string $locale = null): string
    {
        $locale  = $locale ?: app()->getLocale();
        $english = (string) ($this->{$field} ?? '');

        if ($locale === 'en' || $locale === '') {
            return $english;
        }

        $override = data_get($this->translations, $locale . '.' . $field);
        return ($override !== null && $override !== '') ? (string) $override : $english;
    }

    /** Only return pages that should be visible to a public visitor. */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeForNav(Builder $q): Builder
    {
        return $q->published()->where('show_in_nav', true)->orderBy('nav_order');
    }

    public function scopeForFooter(Builder $q): Builder
    {
        return $q->published()->where('show_in_footer', true)->orderBy('nav_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Bust the per-locale nav/footer caches on every save so an
     * operator's edit surfaces on the marketing site immediately
     * rather than waiting up to 60 seconds.
     */
    protected static function booted(): void
    {
        $bust = function () {
            foreach (array_keys(config('locales.supported', ['en' => []])) as $code) {
                \Illuminate\Support\Facades\Cache::forget("static_pages.nav.{$code}");
                \Illuminate\Support\Facades\Cache::forget("static_pages.footer.{$code}");
            }
        };
        static::saved($bust);
        static::deleted($bust);
    }
}
