<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToTenant;

class Form extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'title', 'description',
        'submit_label', 'thank_you_message', 'redirect_url',
        'background_color', 'background_image_url', 'font_family', 'logo_url',
        'multi_step', 'recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key',
        'pipeline_id', 'pipeline_stage_id', 'active',
    ];

    /**
     * Stripped from default array/JSON serialization so any consumer that
     * dumps a Form (REST API, exports, debug responses, queue payloads,
     * etc.) cannot leak the tenant's reCAPTCHA secret. Filament's edit
     * form still reads/writes the column directly via the model attribute
     * — `$hidden` only affects toArray()/toJson(), not Eloquent access.
     */
    protected $hidden = [
        'recaptcha_secret_key',
    ];

    protected $casts = [
        'multi_step'        => 'boolean',
        'recaptcha_enabled' => 'boolean',
        'active'            => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Form $form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->name);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('step_number')->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function getPublicUrlAttribute(): string
    {
        return route('forms.show', [
            'tenant' => $this->tenant?->slug ?? $this->tenant_id,
            'slug'   => $this->slug,
        ]);
    }

    public function getEmbedSnippetAttribute(): string
    {
        $baseUrl = config('app.url');
        return '<script src="' . $baseUrl . '/widget.js" data-form-id="' . $this->id . '" defer></script>';
    }
}
