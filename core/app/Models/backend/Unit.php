<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;


class Unit extends Model
{
  

    protected $fillable = ['name','code','precision','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'precision' => 'integer',
    ];

    // Common scopes
    public function scopeActive($q){ return $q->where('is_active', 1); }
    public function scopeSearch($q, $s){
        if (!$s) return $q;
        return $q->where(function($x) use ($s){
            $x->where('name','like',"%$s%")
              ->orWhere('code','like',"%$s%");
        });
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
