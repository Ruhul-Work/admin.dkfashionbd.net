<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'slug',
        'category_type_id',
        'is_active',
        'icon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image'
    ];

    public function subcategory()
    {
        return $this->hasMany(Subcategory::class);
    }
     public function product()
    {
        return $this->hasMany(Product::class);
    }
    public function category_type()
    {
        return $this->belongsTo(CategoryType::class);
    }
   
}
