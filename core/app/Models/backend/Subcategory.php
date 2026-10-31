<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'is_active',
        'icon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
      public function product()
    {
        return $this->hasMany(Product::class);
    }
}
