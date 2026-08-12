<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TrackingSnippet extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'uuid',
        'allowed_domains',
        'is_active',
        'views_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'allowed_domains' => 'array',
        'is_active'       => 'boolean',
        'views_count'     => 'integer',
        'last_viewed_at'  => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getEmbedSnippetAttribute(): string
    {
        $url = url('/tracking/' . $this->uuid . '/track.js');
        return '<script src="' . $url . '" async></script>';
    }
}
