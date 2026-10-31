<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $fillable = [
        'supplier_id', 'branch_id', 'warehouse_id',
        'po_number', 'status', 'order_date', 'expected_date',
        'currency', 'subtotal', 'tax_amount', 'shipping_amount', 'total_amount','paid_amount', 'outstanding_amount', 'payment_status', 'notes','discount_type', 'discount_value', 'discount_amount', 'purchase_invoice',
        'created_by', 'updated_by',
    ];



    protected $casts = [
        'order_date'      => 'date',
        'expected_date'   => 'date',
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];

    // Relations
    public function supplier(): BelongsTo
    {return $this->belongsTo(Supplier::class);}
    public function branch(): BelongsTo
    {return $this->belongsTo(Branch::class);}
    public function warehouse(): BelongsTo
    {return $this->belongsTo(Warehouse::class);}
    public function items(): HasMany
    {return $this->hasMany(PurchaseOrderItem::class);}
    public function receipts(): HasMany
    {return $this->hasMany(PurchaseReceipt::class);}



    // Helper
    public function getOutstandingAmountAttribute()
    {
        // naive: total - payments (you can implement sum of related payments)
        return $this->total_amount - $this->payments()->sum('amount');
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
