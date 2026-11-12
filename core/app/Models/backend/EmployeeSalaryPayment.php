<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryPayment extends Model
{
    protected $table = 'employee_salary_payments';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}