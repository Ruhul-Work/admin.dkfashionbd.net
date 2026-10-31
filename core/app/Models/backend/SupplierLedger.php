<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierLedger extends Model
{
    protected $table = 'supplier_ledgers';
    protected $fillable = [
        'supplier_id', 'branch_id', 'reference_type', 'reference_id', 'txn_date', 'description', 'debit', 'credit', 'balance_after',
    ];
    protected $casts = [
        'txn_date'      => 'date',
        'debit'         => 'decimal:2',
        'credit'        => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {return $this->belongsTo(Supplier::class);}
}
