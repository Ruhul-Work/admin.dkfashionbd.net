<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferShippingProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_shipping_id',
        'product_id',
    ];

    public function offerShipping()
    {
        return $this->belongsTo(OfferShipping::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
