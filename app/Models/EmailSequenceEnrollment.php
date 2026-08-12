<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSequenceEnrollment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sequence_id',
        'lead_id',
        'current_step',
        'status',
        'enrolled_at',
        'next_send_at',
        'completed_at',
        'unenroll_reason',
    ];

    protected $casts = [
        'current_step' => 'integer',
        'enrolled_at'  => 'datetime',
        'next_send_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class, 'sequence_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
