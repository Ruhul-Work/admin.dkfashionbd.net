<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Upazila extends Model
{
    protected $table = "upazilas";
    protected $fillable =[
        'upazila_name',
        'upazila_bn_name',
        'upazila_district_id',
        'upazila_url'
    ];

    public function upazila_district()
    {
        return $this->belongsTo(District::class, 'upazila_district_id', 'district_id');
    }
}
