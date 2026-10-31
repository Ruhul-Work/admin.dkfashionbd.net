<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
      use SoftDeletes;

    protected $table = 'warehouses';

    protected $fillable = [
        'branch_id','name','code','type','is_default','is_active',
        'phone','email','address','meta'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'meta'       => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
