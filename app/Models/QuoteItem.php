<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'quote_id', 'product_id',
        'name', 'description',
        'quantity', 'unit_price', 'discount_percent', 'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity'         => 'integer',
        'unit_price'       => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total'            => 'decimal:2',
        'sort_order'       => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $item) {
            $unit     = (float) $item->unit_price;
            $qty      = (int)   ($item->quantity ?: 1);
            $discount = (float) $item->discount_percent;
            $item->total = round(($unit * $qty) * (1 - $discount / 100), 2);
        });

        static::saved(function (self $item) {
            $item->quote?->recalculate();
        });

        static::deleted(function (self $item) {
            $item->quote?->recalculate();
        });
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
