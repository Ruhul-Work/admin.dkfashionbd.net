<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'branch_id',
        'invoice_no',
        'posted_at',
        'name',
        'reference',
        'expense_date',
        'description',
        'total_amount',
        'status',          // draft | posted
        'created_by',
    ];

    /* ================= Relations ================= */

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function payment()
    {
        return $this->hasOne(ExpensePayment::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'source');
    }
}