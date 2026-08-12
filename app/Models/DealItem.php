<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'product_id',
        'name',
        'unit_price',
        'quantity',
        'discount_percent',
        'total',
    ];

    protected $casts = [
        'unit_price'        => 'decimal:2',
        'quantity'          => 'integer',
        'discount_percent'  => 'decimal:2',
        'total'             => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $item) {
            $unit     = (float) $item->unit_price;
            $qty      = (int)   $item->quantity;
            $discount = (float) $item->discount_percent;
            $item->total = round(($unit * $qty) * (1 - $discount / 100), 2);
        });

        static::saved(function (self $item) {
            $item->lead?->recalculateDealValue();
        });

        static::deleted(function (self $item) {
            $item->lead?->recalculateDealValue();
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
