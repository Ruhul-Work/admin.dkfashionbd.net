<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //
    protected $table = "districts";

    protected $primaryKey = 'district_id';


    protected $fillable = [
        'district_division_id',
        'district_name',
        'district_bn_name',
        'district_lat',
        'district_lon',
        'district_url',
    ];

    public function district_division()
    {
      return $this->belongsTo(Division::class);
    }

    public function district_upazila()
    {
      return $this->hasMany(Upazila::class, 'upazila_district_id', 'district_id');
    }
}
