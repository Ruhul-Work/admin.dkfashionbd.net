<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $table = 'employee_salaries';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}