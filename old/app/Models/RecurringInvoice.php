<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A recurring invoice ("due") — a tenant's standing monthly/annual charge
 * against a lead/company. The scheduled command `invoices:process-recurring`
 * walks every active schedule whose next_run_date has arrived, materialises
 * a concrete Invoice (+ a single line item) via {@see generateInvoice()},
 * then rolls the schedule forward via {@see advanceSchedule()}.
 *
 * This model deliberately does NOT re-implement invoicing — it leans on the
 * existing Invoice/InvoiceItem creation hooks (auto invoice_number, auto
 * total recalculation) so a generated invoice is indistinguishable from a
 * hand-created one.
 */
class RecurringInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    public const INTERVAL_MONTH = 'month';
    public const INTERVAL_YEAR  = 'year';

    protected $fillable = [
        'tenant_id', 'lead_id', 'company_id',
        'title', 'amount', 'currency',
        'interval', 'anchor_day',
        'next_run_date', 'due_days',
        'auto_send', 'active',
        'last_generated_at', 'notes',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'anchor_day'        => 'integer',
        'due_days'          => 'integer',
        'auto_send'         => 'boolean',
        'active'            => 'boolean',
        'next_run_date'     => 'date',
        'last_generated_at' => 'datetime',
    ];

    /* ------------- Relationships ------------- */

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /* ------------- Scopes ------------- */

    /**
     * Schedules that are active and whose next run date has arrived
     * (today or earlier). Tenant-scoping is applied separately by the
     * caller — the cron passes ->withoutGlobalScope('tenant').
     */
    public function scopeDue(Builder $query): Builder
    {
        return $query
            ->where('active', true)
            ->whereDate('next_run_date', '<=', now()->toDateString());
    }

    /* ------------- Domain actions ------------- */

    /**
     * Materialise a concrete Invoice for this billing period.
     *
     * Assumes the current_tenant container binding is already set to this
     * schedule's tenant (the command binds it before calling) so the
     * BelongsToTenant creating hooks stamp the right tenant_id on the
     * Invoice and InvoiceItem.
     *
     * Creates exactly ONE line item (name=title, qty=1, unit_price=amount);
     * InvoiceItem's saved() hook recalculates the invoice total for us.
     */
    public function generateInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'tenant_id'  => $this->tenant_id,
            'lead_id'    => $this->lead_id,
            'company_id' => $this->company_id,
            'created_by' => null,
            'currency'   => $this->currency,
            'status'     => $this->auto_send ? Invoice::STATUS_SENT : Invoice::STATUS_DRAFT,
            'due_date'   => now()->addDays((int) $this->due_days)->toDateString(),
            'sent_at'    => $this->auto_send ? now() : null,
        ]);

        InvoiceItem::create([
            'tenant_id'  => $this->tenant_id,
            'invoice_id' => $invoice->id,
            'name'       => $this->title,
            'quantity'   => 1,
            'unit_price' => $this->amount,
            'sort_order' => 0,
        ]);

        return $invoice->refresh();
    }

    /**
     * Roll the schedule forward to the next billing period.
     *
     * Bumps next_run_date by one interval (year → +1 year, otherwise
     * +1 month). When an anchor_day is set, snaps the day-of-month to
     * min(anchor_day, daysInMonth) so a "31st" anchor lands on the last
     * day of shorter months instead of overflowing.
     *
     * Uses saveQuietly() so rolling the schedule doesn't fire model
     * events / observers.
     */
    public function advanceSchedule(): void
    {
        $this->last_generated_at = now();

        $next = $this->next_run_date instanceof \Carbon\CarbonInterface
            ? $this->next_run_date->copy()
            : \Illuminate\Support\Carbon::parse($this->next_run_date);

        $next = $this->interval === self::INTERVAL_YEAR
            ? $next->addYear()
            : $next->addMonth();

        if ($this->anchor_day) {
            $day = min((int) $this->anchor_day, $next->daysInMonth);
            $next = $next->day($day);
        }

        $this->next_run_date = $next->toDateString();

        $this->saveQuietly();
    }
}
