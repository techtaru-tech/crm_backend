<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class AuditLog extends Model
{
    use BelongsToTenant;
    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_name',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'tags',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function record(
        string $action,
        mixed $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $tags = null
    ): self {
        $user = auth()->user();

        return self::create([
            'tenant_id'      => $user?->tenant_id,
            'user_id'        => $user?->id,
            'user_name'      => $user?->name,
            'action'         => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id'   => $auditable?->getKey(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'url'            => request()->fullUrl(),
            'tags'           => $tags,
        ]);
    }
}
