<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class EmployeeCurrentBalance extends Model
{
    protected $table = 'employee_current_balances';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'current_balance' => 'decimal:2',
    ];
}