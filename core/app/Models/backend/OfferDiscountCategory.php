<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferDiscountCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_discount_id',
        'category_id',
    ];

    public function offerDiscount()
    {
        return $this->belongsTo(OfferDiscount::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
