<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LeadBot — a single visitor chat session.
 *
 * Tenant-scoped via {@see BelongsToTenant} so it is exported and erased
 * automatically.  Holds no PII directly beyond the (hashed) visitor IP;
 * the captured person, when one is captured, is linked via lead_id.
 */
class ChatConversation extends Model
{
    use HasFactory;
    use BelongsToTenant;

    public const STATUS_OPEN = 'open';
    public const STATUS_CAPTURED = 'captured';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'tenant_id',
        'chatbot_config_id',
        'lead_id',
        'visitor_ip_hash',
        'status',
        'started_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function config(): BelongsTo
    {
        return $this->belongsTo(ChatbotConfig::class, 'chatbot_config_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('id');
    }
}
