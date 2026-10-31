<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class OpeningBalance extends Model
{
     protected $table = 'opening_balances';

    protected $fillable = [
        'fiscal_year_id',
        'branch_id',
        'account_id',
        'amount',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public static function SOURCE_ID()
    {
        
    }
    
}
