<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayment extends Model
{
    protected $fillable = [
        'supplier_id', 'branch_id', 'purchase_order_id', 'purchase_receipt_id',
        'payment_date', 'amount', 'method', 'reference', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {return $this->belongsTo(Supplier::class);}
    public function branch(): BelongsTo
    {return $this->belongsTo(Branch::class);}
    public function order(): BelongsTo
    {return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');}
    public function receipt(): BelongsTo
    {return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id');}
}
