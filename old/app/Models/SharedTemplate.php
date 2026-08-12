<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One row = one template listed in the DFY marketplace.
 *
 * Browse filters: type + category + is_public.
 * Featured row drives the "Editor's picks" carousel.
 *
 * Lifecycle:
 *   - Owner saves a draft (is_public=false)
 *   - Owner flips public (is_public=true) → appears in browse
 *   - Buyer installs → downloads++
 *   - Buyer reviews → rating_avg recomputed nightly
 *   - Operator deletes for ToS violation → softDelete (hidden but
 *     restorable for chargebacks)
 */
class SharedTemplate extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_AUTOMATION       = 'automation';
    public const TYPE_FUNNEL           = 'funnel';
    public const TYPE_EMAIL_SEQUENCE   = 'email_sequence';
    public const TYPE_FORM             = 'form';
    public const TYPE_PIPELINE         = 'pipeline';
    public const TYPE_LANDING_PAGE     = 'landing_page';

    public const TYPES = [
        self::TYPE_AUTOMATION     => 'Automation',
        self::TYPE_FUNNEL         => 'Funnel',
        self::TYPE_EMAIL_SEQUENCE => 'Email sequence',
        self::TYPE_FORM           => 'Form',
        self::TYPE_PIPELINE       => 'Pipeline',
        self::TYPE_LANDING_PAGE   => 'Landing page',
    ];

    /**
     * Translated template-type labels for display.
     * Keys mirror self::TYPES.
     */
    public static function typeLabels(): array
    {
        return [
            self::TYPE_AUTOMATION     => __('models/shared_template.type_automation'),
            self::TYPE_FUNNEL         => __('models/shared_template.type_funnel'),
            self::TYPE_EMAIL_SEQUENCE => __('models/shared_template.type_email_sequence'),
            self::TYPE_FORM           => __('models/shared_template.type_form'),
            self::TYPE_PIPELINE       => __('models/shared_template.type_pipeline'),
            self::TYPE_LANDING_PAGE   => __('models/shared_template.type_landing_page'),
        ];
    }

    protected $fillable = [
        'owner_tenant_id',
        'type',
        'name',
        'description',
        'payload',
        'price',
        'currency',
        'category',
        'is_public',
        'is_featured',
        'downloads',
        'rating_avg',
        'metadata',
    ];

    protected $casts = [
        'payload'     => 'array',
        'price'       => 'decimal:2',
        'is_public'   => 'boolean',
        'is_featured' => 'boolean',
        'downloads'   => 'integer',
        'rating_avg'  => 'decimal:2',
        'metadata'    => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'owner_tenant_id');
    }

    public function scopePublished($q)
    {
        return $q->where('is_public', true);
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }

    public function scopeForType($q, string $type)
    {
        return $q->where('type', $type);
    }

    public function scopeForCategory($q, ?string $category)
    {
        return $category
            ? $q->where('category', $category)
            : $q;
    }

    public function isFree(): bool
    {
        return (float) $this->price <= 0;
    }
}
