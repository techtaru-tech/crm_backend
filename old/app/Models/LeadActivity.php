<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class LeadActivity extends Model
{
    use HasFactory, BelongsToTenant;

    public $timestamps = true;
    public const UPDATED_AT = null;

    protected $fillable = [
        'lead_id', 'tenant_id', 'user_id', 'type', 'description', 'metadata',
    ];

    protected $casts = ['metadata' => 'array'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
