<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferGift extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'buy_product_id',
        'buy_quantity',
        'gift_type',
        'free_product_id',
        'free_quantity',
        'min_purchase_amount'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function buyProduct()                                             
    {
        return $this->belongsTo(Product::class, 'buy_product_id');
    }

    public function freeProduct()
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }
}
