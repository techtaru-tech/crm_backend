<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToTenant;

class OutboundWebhook extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'name',
        'url',
        'events',
        'filters',
        'secret',
        'enabled',
    ];

    protected $casts = [
        'events'  => 'array',
        'filters' => 'array',
        'enabled' => 'boolean',
        // Webhook signing secrets are sensitive — encrypted at rest
        // via Laravel's encrypted cast (uses APP_KEY).  A DB dump
        // therefore cannot be replayed by an attacker without also
        // owning APP_KEY.  Existing dev installs with plaintext rows
        // must regenerate webhooks (release notes).
        'secret'  => 'encrypted',
    ];

    /**
     * The signing secret is stripped from default array/JSON output.
     * A consumer with read:integrations dumping the webhook list would
     * otherwise be handed the HMAC key — which defeats the timestamped
     * signature in signWithTimestamp(). signWithTimestamp() reads the
     * column via direct attribute access, which $hidden does not block.
     */
    protected $hidden = [
        'secret',
    ];

    public const EVENTS = [
        'lead.created'         => 'Lead Created',
        'lead.updated'         => 'Lead Updated',
        'lead.deleted'         => 'Lead Deleted',
        'lead.stage_changed'   => 'Lead Stage Changed',
        'form.submitted'       => 'Form Submitted',
        'automation.triggered' => 'Automation Triggered',
    ];

    /**
     * Translated event labels for Filament Select / display.
     * Keys mirror self::EVENTS (dotted event tokens).
     */
    public static function eventLabels(): array
    {
        return [
            'lead.created'         => __('models/outbound_webhook.event_lead_created'),
            'lead.updated'         => __('models/outbound_webhook.event_lead_updated'),
            'lead.deleted'         => __('models/outbound_webhook.event_lead_deleted'),
            'lead.stage_changed'   => __('models/outbound_webhook.event_lead_stage_changed'),
            'form.submitted'       => __('models/outbound_webhook.event_form_submitted'),
            'automation.triggered' => __('models/outbound_webhook.event_automation_triggered'),
        ];
    }

    /**
     * Supported filter keys and the payload paths they match against.
     * Filter format: { "source": ["facebook","api"], "status": ["new"], "tags": ["Hot Lead"], "pipeline_id": [1,2] }
     * A webhook fires if ALL specified filters match (AND logic), each filter uses OR within its values.
     * The "tags" filter checks if ANY of the lead's tags match ANY of the allowed tag names.
     */
    public const FILTER_FIELDS = ['source', 'status', 'pipeline_id', 'pipeline_stage_id', 'assigned_user_id', 'tags'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class, 'webhook_id');
    }

    public function listensTo(string $event): bool
    {
        return in_array($event, (array) $this->events, true);
    }

    /**
     * Check whether a payload matches this webhook's filters.
     * Returns true if no filters are defined (fire for everything).
     *
     * Logic: ALL filter fields must match (AND). Within each field, OR semantics apply.
     * Special case — "tags": intersection check (lead has at least one of the allowed tags).
     */
    public function matchesFilters(array $payload): bool
    {
        $filters = (array) ($this->filters ?? []);

        if (empty($filters)) {
            return true;
        }

        foreach ($filters as $field => $allowedValues) {
            if (empty($allowedValues)) {
                continue;
            }
            $allowedValues = (array) $allowedValues;

            if ($field === 'tags') {
                // payload['tags'] is an array of tag names; check for any overlap
                $payloadTags = (array) ($payload['tags'] ?? []);
                $intersection = array_intersect(
                    array_map('strtolower', $payloadTags),
                    array_map('strtolower', $allowedValues)
                );
                if (empty($intersection)) {
                    return false;
                }
                continue;
            }

            $payloadValue = $payload[$field] ?? null;

            // Loose comparison to handle int/string mismatches from JSON
            $matched = false;
            foreach ($allowedValues as $allowed) {
                if ((string) $payloadValue === (string) $allowed) {
                    $matched = true;
                    break;
                }
            }

            if (! $matched) {
                return false;
            }
        }

        return true;
    }

    /**
     * Stripe-style replay-safe signing.  Signs "{timestamp}.{body}"
     * concatenation so a captured payload can't be replayed once the
     * consumer's tolerance window passes.
     *
     * Returns:
     *   ['timestamp' => '1714338000',
     *    'signature' => 't=1714338000,v1=<hex>']
     *
     * Verifier on the consumer side should:
     *   1) Reject if |now - t| > tolerance (e.g. 5 min)
     *   2) Recompute hmac_sha256(secret, "{t}.{body}") and compare
     *      to v1 in constant time (hash_equals)
     */
    public function signWithTimestamp(string $payload, ?int $timestamp = null): array
    {
        $ts        = $timestamp ?? time();
        $signed    = $ts . '.' . $payload;
        $signature = hash_hmac('sha256', $signed, $this->secret);

        return [
            'timestamp' => (string) $ts,
            'signature' => sprintf('t=%d,v1=%s', $ts, $signature),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $hook) {
            if (empty($hook->secret)) {
                $hook->secret = Str::random(40);
            }
        });
    }
}
