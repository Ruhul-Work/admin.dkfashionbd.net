<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_amount',
        'applies_to',
        'max_usage_total',
        'max_usage_per_user',
        'usage_count'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function products()
    {
        return $this->hasMany(OfferDiscountProduct::class);
    }

    public function categories()
    {
        return $this->hasMany(OfferDiscountCategory::class);
    }
}
