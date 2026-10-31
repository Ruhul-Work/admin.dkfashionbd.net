<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
      protected $table = 'sale_return_items';
      protected $fillable = [
        'sale_return_id',
        'sale_item_id',
        'product_id',
        'product_variant_id',
        'qty',
        'unit_price',
        'refund_amount',
        'discount_amount',
        'reason',
        'stock_adjusted',
    ];

    protected $casts = [
        'stock_adjusted' => 'boolean',
        'qty'            => 'float',
        'unit_price'     => 'float',
        'refund_amount'  => 'float',
    ];

    /* =============================
     | Relationships
     ============================= */

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
