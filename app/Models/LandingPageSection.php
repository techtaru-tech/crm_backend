<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'type',
        'sort_order',
        'content',
        'is_visible',
    ];

    protected $casts = [
        'content'    => 'array',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const TYPES = [
        'hero'         => 'Hero',
        'features'     => 'Features',
        'form'         => 'Form',
        'testimonials' => 'Testimonials',
        'cta'          => 'Call to Action',
        'gallery'      => 'Gallery',
        'pricing'      => 'Pricing',
        'faq'          => 'FAQ',
        'html'         => 'Custom HTML',
    ];

    /**
     * Translated section-type labels for Filament Select / display.
     * Keys mirror self::TYPES.
     */
    public static function typeLabels(): array
    {
        return [
            'hero'         => __('models/landing_page_section.type_hero'),
            'features'     => __('models/landing_page_section.type_features'),
            'form'         => __('models/landing_page_section.type_form'),
            'testimonials' => __('models/landing_page_section.type_testimonials'),
            'cta'          => __('models/landing_page_section.type_cta'),
            'gallery'      => __('models/landing_page_section.type_gallery'),
            'pricing'      => __('models/landing_page_section.type_pricing'),
            'faq'          => __('models/landing_page_section.type_faq'),
            'html'         => __('models/landing_page_section.type_html'),
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}
