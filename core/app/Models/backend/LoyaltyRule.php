<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class LoyaltyRule extends Model
{
    protected $table = 'loyalty_rules';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];
}
