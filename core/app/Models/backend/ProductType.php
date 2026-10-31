<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'product_types';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
