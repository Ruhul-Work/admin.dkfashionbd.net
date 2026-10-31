<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferShipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'free_shipping_on',
        'shipping_applies_to',
        'min_order_amount',
        'applicable_area',
        'max_shipping_discount'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function customAreas()
    {
        return $this->hasMany(OfferShippingArea::class);
    }

    public function products()
    {
        return $this->hasMany(OfferShippingProduct::class);
    }
}
