<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MeetingBooking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'meeting_type_id',
        'user_id',
        'lead_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'starts_at',
        'ends_at',
        'timezone',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'notes',
        'meeting_url',
        'reschedule_token',
        'view_token',
        'cancel_token',
        'metadata',
    ];

    protected $casts = [
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata'     => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (MeetingBooking $model) {
            // Three SEPARATE tokens — principle of least capability. Leaking
            // one URL (say, the ICS in a shared calendar) doesn't grant the
            // other two actions (cancel, reschedule).
            if (empty($model->reschedule_token)) {
                $model->reschedule_token = Str::random(40);
            }
            if (empty($model->view_token)) {
                $model->view_token = Str::random(40);
            }
            if (empty($model->cancel_token)) {
                $model->cancel_token = Str::random(40);
            }
        });
    }

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function getRescheduleUrlAttribute(): string
    {
        return url('/book/reschedule/' . $this->reschedule_token);
    }

    public function getCancelUrlAttribute(): string
    {
        return url('/book/confirmation/' . $this->view_token);
    }

    public function getIcsUrlAttribute(): string
    {
        return url('/book/ics/' . $this->view_token);
    }
}
