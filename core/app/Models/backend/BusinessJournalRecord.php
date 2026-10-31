<?php
namespace App\Models\backend;

use App\Models\backend\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessJournalRecord extends Model
{
    use HasFactory;

    protected $table = 'business_journal_records';

    protected $fillable = [
        'business_journal_id',
        'payment_type',
        'date',
        'account_id',
        'amount',
        'narration',
        'journal_entry_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function businessJournal()
    {
        return $this->belongsTo(BusinessJournal::class, 'business_journal_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
