<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colors';
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
