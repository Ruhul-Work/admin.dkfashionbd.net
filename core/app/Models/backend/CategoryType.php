<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    protected $table = 'category_types';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function category()
    {
        return $this->hasMany(Category::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

}
