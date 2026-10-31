<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $table = 'business_types';
    protected $fillable = ['name'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_business');
    }
    public function companySetting()
    {
        return $this->hasMany(CompanySetting::class, 'business_type_id');
    }
}
