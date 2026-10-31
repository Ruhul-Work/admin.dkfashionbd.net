<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferShippingArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_shipping_id',
        'area_id',
    ];

    public function offerShipping()
    {
        return $this->belongsTo(OfferShipping::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'area_id');
    }
}
