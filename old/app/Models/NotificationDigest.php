<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationDigest extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'notification_type',
        'data',
        'sent_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function queue(int $userId, string $type, array $data): void
    {
        static::create([
            'user_id'           => $userId,
            'notification_type' => $type,
            'data'              => $data,
        ]);
    }

    public function scopePending($query)
    {
        return $query->whereNull('sent_at');
    }
}
