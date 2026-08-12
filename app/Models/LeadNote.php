<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToTenant;

class LeadNote extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = ['lead_id', 'tenant_id', 'user_id', 'body', 'mentions'];

    protected $casts = ['mentions' => 'array'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
