<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StockAdjustmentItem extends Model
{
    protected $table   = 'stock_adjustment_items';
    public $timestamps = false; // we have created_at/updated_at columns but using manual

    protected $fillable = [
        'adjustment_id',
        'product_id',
        'warehouse_id',
        'branch_id',
        'direction',
        'quantity',
        'unit_cost',
        'note',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'unit_cost'  => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class, 'adjustment_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    
}
