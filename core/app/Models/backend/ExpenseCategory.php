<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
