<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantDomain extends Model
{
    protected $fillable = [
        'tenant_id',
        'domain',
        'verified_at',
        'verification_token',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
