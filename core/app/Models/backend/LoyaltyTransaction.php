<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $table = 'loyalty_transactions';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];
}
