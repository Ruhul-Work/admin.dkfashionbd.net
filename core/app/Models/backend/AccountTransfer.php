<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransfer extends Model
{
    use HasFactory;

    protected $table = 'account_transfers';

    protected $fillable = [
        'branch_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'transfer_date',
        'note',
        'journal_entry_id',
        'created_by',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'transfer_date' => 'date',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |-----------------------------------------------------------------*/

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
