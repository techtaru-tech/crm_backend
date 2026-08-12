<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSequence extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'status',
        'stop_on_reply',
        'stop_on_won',
    ];

    protected $casts = [
        'stop_on_reply' => 'boolean',
        'stop_on_won'   => 'boolean',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(EmailSequenceStep::class, 'sequence_id')->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(EmailSequenceEnrollment::class, 'sequence_id');
    }
}
