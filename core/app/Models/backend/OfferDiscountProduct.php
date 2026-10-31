<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDiscountProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_discount_id',
        'product_id',
    ];

    public function offerDiscount()
    {
        return $this->belongsTo(OfferDiscount::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class,'id','product_id'); 
    }
}
