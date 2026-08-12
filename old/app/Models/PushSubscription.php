<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = ['user_id', 'endpoint', 'endpoint_hash', 'p256dh_key', 'auth_token', 'content_encoding'];

    protected static function booted(): void
    {
        static::saving(function (self $sub) {
            $sub->endpoint_hash = hash('sha256', $sub->endpoint);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toWebPushSubscription(): array
    {
        return [
            'endpoint'        => $this->endpoint,
            'keys'            => [
                'p256dh' => $this->p256dh_key,
                'auth'   => $this->auth_token,
            ],
            'contentEncoding' => $this->content_encoding,
        ];
    }
}
