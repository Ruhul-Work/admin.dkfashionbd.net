<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class CouponProduct extends Model
{
    protected $table = 'coupon_products';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
