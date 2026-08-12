<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureRequest extends Model
{
    use BelongsToTenant;

    public const STATUSES = [
        'open'         => 'Open',
        'planned'      => 'Planned',
        'in_progress'  => 'In Progress',
        'shipped'      => 'Shipped',
        'closed'       => 'Closed',
    ];

    /**
     * Translated status labels for Filament Select / display.
     * Keys mirror self::STATUSES.
     */
    public static function statusLabels(): array
    {
        return [
            'open'        => __('models/feature_request.status_open'),
            'planned'     => __('models/feature_request.status_planned'),
            'in_progress' => __('models/feature_request.status_in_progress'),
            'shipped'     => __('models/feature_request.status_shipped'),
            'closed'      => __('models/feature_request.status_closed'),
        ];
    }

    protected $fillable = [
        'tenant_id', 'user_id', 'title', 'description', 'status', 'votes_count',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(FeatureRequestVote::class);
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }
}
