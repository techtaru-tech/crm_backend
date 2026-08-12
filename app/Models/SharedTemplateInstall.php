<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row = one successful marketplace install.
 *
 * Authoritative source of truth for the per-month install limit
 * enforced by MarketplaceInstaller — replaces the previous brittle
 * "name contains '(from marketplace)'" string match.
 *
 * Uses BelongsToTenant so the standard tenant-scoping global scope
 * applies, which also folds it into TenantErasureService's reflection
 * scan automatically — when a tenant is erased their install audit
 * disappears with them.
 */
class SharedTemplateInstall extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'template_id',
        'entity_type',
        'entity_id',
        'installed_at',
    ];

    protected $casts = [
        'entity_id'    => 'integer',
        'installed_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(SharedTemplate::class, 'template_id');
    }
}
