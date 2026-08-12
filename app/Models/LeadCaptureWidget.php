<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToTenant;

class LeadCaptureWidget extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'uuid', 'tenant_id', 'pipeline_id', 'pipeline_stage_id',
        'name', 'headline', 'subheadline', 'button_text', 'success_message',
        'primary_color', 'text_color', 'position',
        'show_phone', 'show_company', 'show_message',
        'require_phone', 'require_message',
        'is_active', 'allowed_domains', 'leads_captured',
    ];

    protected $casts = [
        'show_phone'      => 'boolean',
        'show_company'    => 'boolean',
        'show_message'    => 'boolean',
        'require_phone'   => 'boolean',
        'require_message' => 'boolean',
        'is_active'       => 'boolean',
        'allowed_domains' => 'array',
        'leads_captured'  => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function getEmbedSnippetAttribute(): string
    {
        $url = url('/widget/' . $this->uuid . '/loader.js');
        return '<script src="' . $url . '" async></script>';
    }
}
