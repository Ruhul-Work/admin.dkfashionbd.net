<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class PaperQuality extends Model
{
    protected $table = 'paper_qualities';
    protected $fillable = [
        'name',
        'code',
        'is_active',

    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
