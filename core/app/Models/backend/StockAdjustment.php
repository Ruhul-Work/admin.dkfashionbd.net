<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    protected $table = 'stock_adjustments';

    protected $fillable = [
        'reference_no',
        'branch_id',
        'warehouse_id',
        'adjust_date',
        'reason_code',
        'note',
        'status',
        'created_by',
        'approved_by',
        'posted_at',
    ];

    protected $casts = [
        'adjust_date' => 'datetime',
        'posted_at'   => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class, 'adjustment_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function ledgerEntries()
    {
        // assuming stock_ledgers.ref_type = 'stock_adjustment' and ref_id = stock_adjustments.id
        // if your ledger stores ref_type/ref_id, use the where condition below, else adapt.
        return $this->hasMany(StockLedger::class, 'ref_id', 'id')
            ->where('ref_type', 'ADJUSTMENT')
            ->orderBy('txn_date', 'desc');
    }

}
