<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class SaleReturnPayment extends Model
{
     protected $table = 'sale_return_payments';
     protected $fillable = [
        'sale_return_id',
        'branch_id',
        'account_id',
        'payment_type_id',
        'amount',
        'payment_date',
        'reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'float',
        'payment_date' => 'date',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
