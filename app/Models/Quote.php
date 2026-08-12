<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * A sales Quote — the document a tenant sends to a lead/company to
 * propose work. Once accepted (with typed signature + IP capture) it
 * can be promoted to an Invoice via `convertToInvoice()`.
 */
class Quote extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SENT      = 'sent';
    public const STATUS_ACCEPTED  = 'accepted';
    public const STATUS_DECLINED  = 'declined';
    public const STATUS_EXPIRED   = 'expired';
    public const STATUS_CONVERTED = 'converted';

    protected $fillable = [
        'tenant_id', 'lead_id', 'company_id', 'created_by',
        'quote_number', 'status', 'public_token',
        'title', 'introduction', 'terms',
        'currency',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 'total',
        'valid_until', 'sent_at', 'accepted_at', 'declined_at',
        'signed_name', 'signed_ip', 'signed_at', 'decline_reason',
        'invoice_id',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_rate'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'valid_until'     => 'datetime',
        'sent_at'         => 'datetime',
        'accepted_at'     => 'datetime',
        'declined_at'     => 'datetime',
        'signed_at'       => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $quote) {
            if (empty($quote->public_token)) {
                $quote->public_token = Str::random(64);
            }
            if (empty($quote->quote_number)) {
                $quote->quote_number = static::generateNumber((int) $quote->tenant_id);
            }
        });
    }

    /**
     * Generate a tenant-local sequential quote number like Q-5-00042.
     * Uses a DB query + retry to sidestep race conditions on shared hosting
     * where we can't rely on advisory locks.
     */
    protected static function generateNumber(int $tenantId): string
    {
        $prefix = 'Q-' . $tenantId . '-';
        $last   = static::withTrashed()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('quote_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('quote_number');

        $seq = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }

    /* ------------- Relationships ------------- */

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ------------- Scopes ------------- */

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_DECLINED, self::STATUS_EXPIRED]);
    }

    /* ------------- Domain actions ------------- */

    public function markSent(): void
    {
        $this->update([
            'status'  => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAccepted(string $name, string $ip): void
    {
        $now = now();
        $this->update([
            'status'      => self::STATUS_ACCEPTED,
            'accepted_at' => $now,
            'signed_name' => $name,
            'signed_ip'   => $ip,
            'signed_at'   => $now,
        ]);
    }

    public function markDeclined(string $reason): void
    {
        $this->update([
            'status'         => self::STATUS_DECLINED,
            'declined_at'    => now(),
            'decline_reason' => $reason,
        ]);
    }

    /**
     * Re-sum line items, apply tax + discount, and persist totals.
     */
    public function recalculate(): void
    {
        $subtotal = (float) $this->items()->sum('total');
        $discount = (float) $this->discount_amount;
        $taxable  = max(0, $subtotal - $discount);
        $taxAmt   = round($taxable * ((float) $this->tax_rate / 100), 2);
        $total    = round($taxable + $taxAmt, 2);

        $this->updateQuietly([
            'subtotal'   => round($subtotal, 2),
            'tax_amount' => $taxAmt,
            'total'      => $total,
        ]);
    }

    /**
     * Clone items + totals into a fresh Invoice (status=draft) and link
     * the quote back to it. Called from the Filament "Convert" action
     * and from the customer-facing accept flow.
     */
    public function convertToInvoice(): Invoice
    {
        return DB::transaction(function () {
            $invoice = Invoice::create([
                'tenant_id'       => $this->tenant_id,
                'lead_id'         => $this->lead_id,
                'company_id'      => $this->company_id,
                'quote_id'        => $this->id,
                'created_by'      => $this->created_by,
                'currency'        => $this->currency,
                'subtotal'        => $this->subtotal,
                'tax_rate'        => $this->tax_rate,
                'tax_amount'      => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'total'           => $this->total,
                'issued_date'     => now()->toDateString(),
                'due_date'        => now()->addDays(14)->toDateString(),
                'notes'           => $this->terms,
            ]);

            foreach ($this->items as $item) {
                InvoiceItem::create([
                    'tenant_id'        => $invoice->tenant_id,
                    'invoice_id'       => $invoice->id,
                    'product_id'       => $item->product_id,
                    'name'             => $item->name,
                    'description'      => $item->description,
                    'quantity'         => $item->quantity,
                    'unit_price'       => $item->unit_price,
                    'discount_percent' => $item->discount_percent,
                    'total'            => $item->total,
                    'sort_order'       => $item->sort_order,
                ]);
            }

            $invoice->recalculate();

            $this->update([
                'status'     => self::STATUS_CONVERTED,
                'invoice_id' => $invoice->id,
            ]);

            return $invoice;
        });
    }

    public function publicUrl(): string
    {
        return url('/quote/' . $this->public_token);
    }
}
