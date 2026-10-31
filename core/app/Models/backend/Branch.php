<?php
namespace App\Models\backend;

use App\Models\backend\BusinessType;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
   

    protected $table    = 'branches';
    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    public function businessTypes()
    {
        return $this->belongsToMany(BusinessType::class, 'branch_business');
    }

}
