<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    //
    protected $table = "divisions";
    protected $fillable = [
        'name',
        'bn_name',
        'url'
    ];
    public function division_district()
    {
        return $this->hasMany(District::class);
    }
}
