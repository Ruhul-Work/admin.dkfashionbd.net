<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceiptItem extends Model
{
    protected $fillable = [
        'receipt_id', 'product_id', 'quantity', 'unit_cost', 'uom_id',
    ];

    protected $casts = [
        'quantity'  => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    public function receipt(): BelongsTo
    {return $this->belongsTo(PurchaseReceipt::class, 'receipt_id');}
    public function product(): BelongsTo
    {return $this->belongsTo(Product::class);}
    public function uom(): BelongsTo
    {return $this->belongsTo(Unit::class, 'uom_id');}
}
