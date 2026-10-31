<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = [
        'expense_id',
        'expense_category_id',
        'description',
        'amount',
    ];

    /* ================= Relations ================= */

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
