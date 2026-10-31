<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SaleItem extends Model
{
    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'name',
        'image',
        'product_variant_id',
        'unit_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'line_total',
        'lot_number',
        'expiry_date',
    ];

    /* ================= Relations ================= */

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /* ================= Helpers ================= */

    public function calculateLineTotal(): float
    {
        return round(
            ($this->unit_price * $this->quantity)
            - $this->discount_amount
            + $this->tax_amount,
            2
        );
    }
}
