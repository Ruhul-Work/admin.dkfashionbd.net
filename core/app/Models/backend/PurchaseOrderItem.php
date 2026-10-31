<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'sku', 'description',
        'unit_cost', 'quantity', 'received_quantity', 'line_total',
    ];

    protected $casts = [
        'unit_cost'         => 'decimal:2',
        'line_total'        => 'decimal:2',
        'quantity'          => 'integer',
        'received_quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');}
    public function product(): BelongsTo
    {return $this->belongsTo(Product::class);}

    public function getRemainingQuantityAttribute()
    {
        return max(0, $this->quantity - $this->received_quantity);
    }
}
