<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    protected $table = 'coupon_users';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function Customer()
    {
        return $this->belongsTo(Customer::class,'user_id');
    }
}
