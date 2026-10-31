<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table   = 'customers';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function coupon()
    {
        return $this->belongsToMany(CouponUser::class,'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
