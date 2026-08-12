<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LandingPage extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'title',
        'meta_description',
        'og_image_url',
        'favicon_url',
        'status',
        'form_id',
        'pipeline_id',
        'pipeline_stage_id',
        'primary_color',
        'background_color',
        'font_family',
        'views_count',
        'conversions_count',
        'redirect_on_submit',
        'custom_css',
        'custom_js',
    ];

    protected $casts = [
        'views_count'       => 'integer',
        'conversions_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (LandingPage $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->name);
            }
            if (empty($page->status)) {
                $page->status = 'draft';
            }
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(LandingPageSection::class)->orderBy('sort_order');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    /**
     * Public URL for the landing page at /{workspace-slug}/{page-slug}.
     * Falls back to the legacy /p/{slug} pattern if the tenant is
     * missing a slug (shouldn't happen post-migration, but keeps
     * old rows from throwing on render).
     */
    public function getPublicUrlAttribute(): string
    {
        $tenantSlug = $this->tenant?->slug;

        if ($tenantSlug) {
            return url('/' . $tenantSlug . '/' . $this->slug);
        }

        return url('/p/' . $this->slug);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
