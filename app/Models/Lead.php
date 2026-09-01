<?php

namespace App\Models;

use App\Enums\LeadSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToTenant;

class Lead extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'source',
        'source_id',
        'source_connection_id',
        'form_id',
        'pipeline_id',
        'pipeline_stage_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_normalized',
        'status',
        'is_duplicate',
        'is_starred',
        'lead_score',
        'applied_scoring_rules',
        'deal_value',
        'deal_currency',
        'expected_close_date',
        'won_value',
        'won_at',
        'lost_reason',
        'lost_at',
        'raw_data',
        'custom_fields',
        'assigned_to',
        'assigned_user_id',
        'assigned_team_id',
        'priority',
        'next_follow_up_at',
        'notes',
        'contacted_at',
        'consented_at',
        'consent_text',
        'stage_entered_at',
        'company',
        'job_title',
        'linkedin_url',
        'country',
        'city',
        'industry',
        'company_size',
        'enriched_at',
        'enrichment_data',
        'ai_sdr_opted_out_at',
        'ai_sdr_opt_out_reason',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'landing_page',
        'referrer_url',
    ];

    protected $casts = [
        // H7: typed status — see App\Enums\LeadStatus.  The migration
        // 2026_05_02_000001_normalise_lead_status_vocabulary already
        // coerced every legacy 'converted' → 'won' and 'proposal' →
        // 'qualified', so every row in leads.status now matches an
        // enum case.  Eloquent will throw ValueError on read for any
        // future legacy value, which is the desired fail-loud
        // behaviour.
        'status'              => \App\Enums\LeadStatus::class,
        // Phase 1 funnel fields — see 2026_08_26_000003 migration.
        'priority'            => \App\Enums\LeadPriority::class,
        'next_follow_up_at'   => 'datetime',
        'raw_data'            => 'array',
        'custom_fields'       => 'array',
        'is_duplicate'        => 'boolean',
        'is_starred'          => 'boolean',
        'enriched_at'         => 'datetime',
        'enrichment_data'     => 'array',
        'ai_sdr_opted_out_at' => 'datetime',
        'lead_score'          => 'integer',
        'applied_scoring_rules' => 'array',
        'contacted_at'        => 'datetime',
        'consented_at'        => 'datetime',
        'stage_entered_at'    => 'datetime',
        'deal_value'          => 'decimal:2',
        'won_value'           => 'decimal:2',
        'expected_close_date' => 'date',
        'won_at'              => 'datetime',
        'lost_at'             => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Row-level visibility (spec §3).  Registered here rather than at
        // each call site — see LeadVisibilityScope for why.
        static::addGlobalScope(new \App\Models\Scopes\LeadVisibilityScope());

        static::saving(function (self $lead) {
            if (isset($lead->phone) && $lead->phone !== null) {
                $lead->phone_normalized = preg_replace('/\D/', '', $lead->phone) ?: null;
            } else {
                $lead->phone_normalized = null;
            }
        });

        static::creating(function (self $lead) {
            // Auto-attach a company by email domain (when not already set).
            if (empty($lead->company_id) && ! empty($lead->email)) {
                $tenantId = $lead->tenant_id
                    ?? \App\Support\TenantContext::currentId();

                if ($tenantId) {
                    $company = Company::findOrCreateByDomain($lead->email, (int) $tenantId);
                    if ($company) {
                        $lead->company_id = $company->id;
                    }
                }
            }
        });
    }

    /**
     * Recompute `next_follow_up_at` from the earliest still-open follow-up.
     *
     * Denormalised on purpose: the lead list sorts and filters on this, and a
     * correlated subquery per row was the alternative.  LeadTask calls this on
     * every save/delete, so the column is derived state, never authored.
     * Written with updateQuietly() so it cannot recurse through the observers.
     */
    public function refreshNextFollowUp(): void
    {
        $next = $this->tasks()->withoutGlobalScopes()
            ->whereIn('status', \App\Enums\FollowUpStatus::openValues())
            ->whereNotNull('due_at')
            ->min('due_at');

        if ((string) $this->next_follow_up_at === (string) $next) {
            return;
        }

        $this->updateQuietly(['next_follow_up_at' => $next]);
    }

    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }
        return preg_replace('/\D/', '', $phone) ?: null;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The sales team that owns this lead.
     *
     * Independent of assigned_user_id: handing a lead from one rep to
     * another inside the same team must not change which team owns it,
     * and a lead can sit in a team's pool with no rep yet.
     */
    public function assignedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function stageHistories(): HasMany
    {
        return $this->hasMany(LeadStageHistory::class)->latest('created_at');
    }

    public function assignmentHistories(): HasMany
    {
        return $this->hasMany(LeadAssignmentHistory::class)->latest('created_at');
    }

    /**
     * The Company entity this lead belongs to.
     *
     * Named `companyEntity` intentionally — the `company` magic accessor is
     * shadowed by the existing `leads.company` string column (used as a
     * denormalized name stash by legacy integrations).  Call
     * `$lead->companyEntity` for the linked Company record,
     * `$lead->company` for the string field.
     */
    public function companyEntity(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function sourceConnection(): BelongsTo
    {
        return $this->belongsTo(LeadSourceConnection::class, 'source_connection_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'lead_tag');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest('created_at');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(LeadDuplicate::class, 'original_lead_id');
    }

    public function duplicateOf(): HasMany
    {
        return $this->hasMany(LeadDuplicate::class, 'duplicate_lead_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(LeadTask::class)->orderBy('due_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LeadAttachment::class);
    }

    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function dealItems(): HasMany
    {
        return $this->hasMany(DealItem::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class)->latest('created_at');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest('created_at');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(LeadEmail::class);
    }

    public function sequenceEnrollments(): HasMany
    {
        return $this->hasMany(EmailSequenceEnrollment::class);
    }

    public function aiSdrEnrollments(): HasMany
    {
        return $this->hasMany(AiSdrEnrollment::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LeadMessage::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(LeadCall::class);
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Determine whose turn it is to follow up.
     *
     * Returns:
     *   'new'   → no contact either way yet
     *   'us'    → they reached out, we still owe them a reply
     *   'them'  → we reached out, we're waiting on their response
     */
    public function getWaitingOnAttribute(): string
    {
        // Fast path — when the LeadResource query has been wrapped in
        // `->withWaitingOnState()`, the latest inbound/outbound
        // timestamps are materialized as scalar columns on the row by
        // a single subquery JOIN.  Reading them avoids the per-row
        // `lead_activities` round-trips that previously turned a
        // 50-lead table page (polled every 30s) into a 100-query storm.
        $latestInbound  = $this->getAttribute('_last_inbound_at');
        $latestOutbound = $this->getAttribute('_last_outbound_at');

        if ($latestInbound !== null || $latestOutbound !== null) {
            $lastInbound  = $latestInbound  ? \Illuminate\Support\Carbon::parse($latestInbound)  : null;
            $lastOutbound = $latestOutbound ? \Illuminate\Support\Carbon::parse($latestOutbound) : null;
        } else {
            // Fallback for callers that read $lead->waiting_on outside
            // the table context (a single-Lead view, an audit script,
            // a Mail blade) — keep the original behaviour so the
            // accessor still works without scope opt-in.
            $lastInbound = $this->activities()
                ->whereIn('type', ['email_received', 'message_received', 'call_inbound'])
                ->latest('created_at')
                ->first()?->created_at;

            $lastOutbound = $this->activities()
                ->whereIn('type', ['email_sent', 'message_sent', 'call_outbound', 'note_added'])
                ->latest('created_at')
                ->first()?->created_at;
        }

        if (! $lastInbound && ! $lastOutbound) {
            return 'new';
        }
        if (! $lastInbound) {
            return 'them';
        }
        if (! $lastOutbound) {
            return 'us';
        }
        return $lastInbound->gt($lastOutbound) ? 'us' : 'them';
    }

    /**
     * Eager-add the two timestamps the waiting_on accessor needs as
     * scalar columns on each row, in a single subquery JOIN.  Use
     * this on any list query that renders the `waiting_on` column to
     * collapse a 100-query storm into one query.
     */
    public function scopeWithWaitingOnState(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->addSelect([
            '_last_inbound_at' => \App\Models\LeadActivity::query()
                ->select('created_at')
                ->whereColumn('lead_id', 'leads.id')
                ->whereIn('type', ['email_received', 'message_received', 'call_inbound'])
                ->latest('created_at')
                ->limit(1),
            '_last_outbound_at' => \App\Models\LeadActivity::query()
                ->select('created_at')
                ->whereColumn('lead_id', 'leads.id')
                ->whereIn('type', ['email_sent', 'message_sent', 'call_outbound', 'note_added'])
                ->latest('created_at')
                ->limit(1),
        ]);
    }

    public function getSourceLabelAttribute(): string
    {
        $enum = LeadSource::tryFrom($this->source);
        if ($enum) {
            return $enum->label();
        }

        // Translator-first fallback: look up
        // `lang/en/lead_sources.<source_key>` so non-enum lead sources
        // (custom values introduced by integrations) still pick up
        // localised labels when present.  ucfirst() of the raw key is
        // the last-resort English fallback.
        $rawKey   = (string) ($this->source ?? '');
        $lookup   = 'lead_sources.' . strtolower($rawKey);
        $resolved = __($lookup);

        return $resolved !== $lookup ? (string) $resolved : ucfirst($rawKey);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Weighted deal value = deal_value * (stage win_probability / 100).
     */
    public function weightedValue(): float
    {
        $stage = $this->pipelineStage;
        $probability = $stage?->win_probability;
        if ($probability === null || ! $this->deal_value) {
            return 0.0;
        }
        return round(((float) $this->deal_value) * ((int) $probability / 100), 2);
    }

    /**
     * Recalculate `deal_value` from the sum of all related DealItem totals.
     */
    public function recalculateDealValue(): void
    {
        $sum = (float) $this->dealItems()->sum('total');
        // updateQuietly to avoid recursion into boot hooks
        $this->updateQuietly(['deal_value' => $sum]);
    }
}
