<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ledger row for every (pending/succeeded/failed/refunded) payment
 * attempt made against an Invoice. On create-with-succeeded the parent
 * Invoice's amount_paid + status are recomputed automatically.
 */
class InvoicePayment extends Model
{
    use HasFactory, BelongsToTenant;

    public const STATUS_PENDING   = 'pending';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_REFUNDED  = 'refunded';

    protected $fillable = [
        'tenant_id', 'invoice_id',
        'gateway', 'external_id',
        'amount', 'currency', 'status',
        'meta', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'meta'    => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $payment) {
            if ($payment->status === self::STATUS_SUCCEEDED) {
                $payment->invoice?->recordPayment($payment);
            }
        });

        static::updated(function (self $payment) {
            if ($payment->wasChanged('status') && $payment->status === self::STATUS_SUCCEEDED) {
                $payment->invoice?->recordPayment($payment);
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
