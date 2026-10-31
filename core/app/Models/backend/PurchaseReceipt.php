<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReceipt extends Model
{
    protected $fillable = [
        'supplier_id', 'branch_id', 'warehouse_id', 'purchase_order_id',
        'receipt_date', 'invoice_no', 'note', 'created_by',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {return $this->belongsTo(Supplier::class);}
    public function warehouse(): BelongsTo
    {return $this->belongsTo(Warehouse::class);}
    public function order(): BelongsTo
    {return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');}
    public function items(): HasMany
    {return $this->hasMany(PurchaseReceiptItem::class, 'receipt_id');}

    // convenience: total computed from items
    public function getTotalAttribute()
    {
        return $this->items()->sum(\DB::raw('quantity * unit_cost'));
    }
}
