<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LeadBot — a single turn in a chat conversation.
 *
 * Intentionally NOT tenant-scoped (mirrors {@see FormField}): ownership is
 * derived through the parent {@see ChatConversation}.  The DB FK is
 * cascadeOnDelete so erasing a tenant's conversations removes these rows.
 */
class ChatMessage extends Model
{
    use HasFactory;

    public const ROLE_USER = 'user';
    public const ROLE_ASSISTANT = 'assistant';
    public const ROLE_SYSTEM = 'system';

    protected $fillable = [
        'chat_conversation_id',
        'role',
        'content',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }
}
