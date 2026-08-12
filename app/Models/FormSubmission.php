<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant;

class FormSubmission extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'form_id', 'tenant_id', 'lead_id', 'ip_address', 'user_agent',
        'is_spam', 'consented_at', 'consent_text', 'completed_step',
    ];

    protected $casts = [
        'is_spam'      => 'boolean',
        'consented_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(FormSubmissionValue::class);
    }
}
