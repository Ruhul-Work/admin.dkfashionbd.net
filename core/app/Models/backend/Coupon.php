<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function product()
    {
        return $this->belongsToMany(Product::class);
    }

    public function customer()
    {
        return $this->belongsToMany(Customer::class,'user_id');
    }


}
