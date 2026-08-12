<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

/**
 * Tracks a CSV/Excel company-import batch (status + per-batch counters).
 * The import-tracking shape is entity-agnostic — identical to LeadImport.
 */
class CompanyImport extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'file_path',
        'original_filename',
        'column_mapping',
        'status',
        'total_rows',
        'imported_count',
        'duplicate_count',
        'error_count',
        'errors',
    ];

    protected $casts = [
        'column_mapping' => 'array',
        'errors'         => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
