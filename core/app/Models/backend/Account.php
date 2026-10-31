<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'account_type_id',
        'description',
        'currency',
        'bank_name',
        'bank_account_no',
        'bank_details',
        'allow_negative',
        'is_active',
    ];

    // ✅ ADD THIS RELATIONSHIP

    public function branchAccounts()
    {
        return $this->hasMany(BranchAccount::class, 'account_id');
    }

    public function openingBalances()
    {
        return $this->hasMany(OpeningBalance::class, 'account_id');
    }

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class, 'account_id');
    }
}
