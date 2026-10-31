<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    protected $fillable = [
        'expense_id',
        'payment_type_id',
        'amount',
        'note',
    ];

    /* ================= Relations ================= */

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
