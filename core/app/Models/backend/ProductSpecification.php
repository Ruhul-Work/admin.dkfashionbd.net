<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $table = 'product_specifications';
    protected $fillable = [
        'product_id',
        'material',
        'weight_gsm',
        'fabric_type',
        'origin_country',
        'fabric_weave',
        'care_instructions',
        'durability_rating',
        'transparency_text',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected $hidden = [
        'created_at',
        'updated_at',
        'id',
        'product_id'
    ];
}
