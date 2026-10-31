<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class BranchAccount extends Model
{
    protected $table = 'branch_accounts';

    protected $fillable = [
        'branch_id',
        'account_id',
        'is_active',
        'is_default',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
