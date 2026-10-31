<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{

     protected $table = 'sale_returns';
     protected $fillable = [
        'sale_id',
        'customer_id',
        'branch_id',
        'warehouse_id',
        'total_refund',
        'return_date',
        'status',
        'notes',
        'created_by',
        'approved_by',
    ];

    /* =============================
     | Relationships
     ============================= */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SaleReturnPayment::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
