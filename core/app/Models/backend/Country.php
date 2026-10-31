<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    //
    //  use SoftDeletes;

     protected $table = 'countries';
    protected $fillable = [
        'name',
      
    ];
}
