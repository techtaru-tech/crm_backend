<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MeetingType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'slug',
        'description',
        'duration_minutes',
        'buffer_minutes',
        'advance_notice_hours',
        'future_days_max',
        'color',
        'location_type',
        'location_details',
        'pipeline_id',
        'pipeline_stage_id',
        'is_active',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'duration_minutes'     => 'integer',
        'buffer_minutes'       => 'integer',
        'advance_notice_hours' => 'integer',
        'future_days_max'      => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (MeetingType $model) {
            if (empty($model->slug) && ! empty($model->name)) {
                $base = Str::slug($model->name);
                $slug = $base;
                $i = 1;
                while (static::withoutGlobalScope('tenant')
                    ->where('tenant_id', $model->tenant_id)
                    ->where('slug', $slug)
                    ->exists()
                ) {
                    $slug = $base . '-' . (++$i);
                }
                $model->slug = $slug;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(MeetingBooking::class);
    }

    public function getBookingUrlAttribute(): string
    {
        return url('/book/' . $this->user_id . '/' . $this->slug);
    }

    public function getEmbedSnippetAttribute(): string
    {
        $url = $this->booking_url;

        // EMBED CODE EXCEPTION: this iframe HTML is the snippet tenants
        // paste into external websites. Inline styles are REQUIRED so
        // the embed renders correctly without depending on any of our
        // CSS files being loaded on the target site. Do NOT extract
        // these styles to a stylesheet — doing so would break every
        // existing embed already deployed on customer sites.
        return '<iframe src="' . e($url) . '" width="100%" height="720" style="border:0;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.08);" loading="lazy" title="' . e($this->name) . '"></iframe>';
    }
}
