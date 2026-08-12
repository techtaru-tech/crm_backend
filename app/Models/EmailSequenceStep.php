<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSequenceStep extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sequence_id',
        'sort_order',
        'delay_days',
        'delay_hours',
        'subject',
        'body_html',
    ];

    protected $casts = [
        'sort_order'  => 'integer',
        'delay_days'  => 'integer',
        'delay_hours' => 'integer',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class, 'sequence_id');
    }
}
