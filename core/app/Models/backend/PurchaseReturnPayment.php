<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnPayment extends Model
{
    protected $table = 'purchase_return_payments';
    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ]; 

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }
     public function branch(): BelongsTo
    {return $this->belongsTo(Branch::class);}
     public function supplier(): BelongsTo
    {return $this->belongsTo(Supplier::class);}
}
