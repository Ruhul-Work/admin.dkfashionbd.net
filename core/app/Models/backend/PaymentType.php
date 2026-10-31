<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $table = 'payment_types';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'

    ];

    
}
