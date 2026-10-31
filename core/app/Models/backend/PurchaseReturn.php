<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_returns';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function purchaseReturnPayment()
    {
        return $this->hasMany(PurchaseReturnPayment::class, 'purchase_return_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }
}
