<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $table = 'purchase_return_items';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
