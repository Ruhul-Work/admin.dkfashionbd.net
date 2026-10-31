<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use BranchScoped;

class StockCurrent extends Model
{
      protected $table = 'stock_currents';
    // public $timestamps = false; 

    
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'branch_id',
        'quantity',
        'version',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'quantity' => 'float',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}