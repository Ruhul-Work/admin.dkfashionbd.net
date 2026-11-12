<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class EmployeeAccountLedger extends Model
{
    protected $table = 'employee_account_ledgers';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}