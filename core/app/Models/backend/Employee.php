<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'joining_date' => 'date',
    ];
}