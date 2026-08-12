<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * LeadBot configuration — one embeddable AI chat bubble per row.
 *
 * Tenant-scoped via {@see BelongsToTenant}.  The random `uuid` is the
 * public access key the embed snippet exposes (mirrors
 * {@see LeadCaptureWidget}).  AI generation always runs on the
 * super-admin / global OpenAI key (see ChatbotResponder); there is no
 * per-bot key column on purpose.
 */
class ChatbotConfig extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'pipeline_id',
        'pipeline_stage_id',
        'meeting_type_id',
        'enabled',
        'name',
        'persona',
        'knowledge',
        'greeting',
        'primary_color',
        'daily_message_cap',
    ];

    protected $casts = [
        'enabled'           => 'boolean',
        'daily_message_cap' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
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

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }

    /**
     * One-line <script> embed the tenant pastes into their own site.
     * `async` so it never blocks the host page's render.
     */
    public function getEmbedSnippetAttribute(): string
    {
        $url = url('/chatbot/' . $this->uuid . '/loader.js');

        return '<script src="' . $url . '" async></script>';
    }
}
