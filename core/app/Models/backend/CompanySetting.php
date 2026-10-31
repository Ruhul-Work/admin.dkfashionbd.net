<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $table = 'company_settings';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    public function business_type()
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }
}
