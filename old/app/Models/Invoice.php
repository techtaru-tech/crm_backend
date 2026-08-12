<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * An Invoice — issued to a lead/company for payment. Can originate
 * from a Quote (see Quote::convertToInvoice) or be created directly.
 * Tracks partial payments via InvoicePayment.
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SENT      = 'sent';
    public const STATUS_PARTIAL   = 'partial';
    public const STATUS_PAID      = 'paid';
    public const STATUS_OVERDUE   = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED  = 'refunded';

    protected $fillable = [
        'tenant_id', 'lead_id', 'company_id', 'quote_id', 'created_by',
        'invoice_number', 'status', 'public_token',
        'currency',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 'total',
        'amount_paid',
        'issued_date', 'due_date',
        'sent_at', 'paid_at', 'due_reminder_sent_at',
        'notes', 'billing_address',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_rate'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'amount_paid'     => 'decimal:2',
        'issued_date'     => 'date',
        'due_date'        => 'date',
        'sent_at'              => 'datetime',
        'paid_at'              => 'datetime',
        'due_reminder_sent_at' => 'datetime',
        'billing_address'      => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $invoice) {
            if (empty($invoice->public_token)) {
                $invoice->public_token = Str::random(64);
            }
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateNumber((int) $invoice->tenant_id);
            }
            if (empty($invoice->issued_date)) {
                $invoice->issued_date = now()->toDateString();
            }
        });
    }

    protected static function generateNumber(int $tenantId): string
    {
        $prefix = 'INV-' . $tenantId . '-';
        $last   = static::withTrashed()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('invoice_number');

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

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->latest('created_at');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ------------- Domain actions ------------- */

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

        $this->syncStatusFromPayments();
    }

    /**
     * Pull in a successful payment — bump amount_paid, compute status.
     */
    public function recordPayment(InvoicePayment $payment): void
    {
        if ($payment->status !== 'succeeded') {
            return;
        }

        $paid = (float) $this->payments()
            ->where('status', 'succeeded')
            ->sum('amount');

        $this->amount_paid = round($paid, 2);

        if ($this->amount_paid >= (float) $this->total && (float) $this->total > 0) {
            $this->status  = self::STATUS_PAID;
            $this->paid_at = $payment->paid_at ?: now();
        } elseif ($this->amount_paid > 0) {
            $this->status = self::STATUS_PARTIAL;
        }

        $this->saveQuietly();
    }

    protected function syncStatusFromPayments(): void
    {
        // Only auto-advance out of draft/partial states.
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_REFUNDED], true)) {
            return;
        }

        $paid = (float) $this->amount_paid;
        $total = (float) $this->total;

        if ($total > 0 && $paid >= $total) {
            $this->status = self::STATUS_PAID;
            if (! $this->paid_at) {
                $this->paid_at = now();
            }
        } elseif ($paid > 0) {
            $this->status = self::STATUS_PARTIAL;
        }

        $this->saveQuietly();
    }

    public function markSent(): void
    {
        $this->update([
            'status'  => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function amountDue(): float
    {
        return max(0, round((float) $this->total - (float) $this->amount_paid, 2));
    }

    public function publicUrl(): string
    {
        return url('/invoice/' . $this->public_token);
    }
}
