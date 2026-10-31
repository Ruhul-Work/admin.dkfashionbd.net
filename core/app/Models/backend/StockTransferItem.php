<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransferItem extends Model
{
     use HasFactory;

    protected $table = 'stock_transfer_items';

    protected $fillable = [
        'transfer_id',
        'product_id',
        'quantity',
        'unit_cost',
        
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

  
    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'transfer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
