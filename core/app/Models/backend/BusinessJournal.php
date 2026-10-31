<?php
namespace App\Models\backend;

use App\Models\backend\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessJournal extends Model
{
    use HasFactory;

    protected $table = 'business_journals';

    protected $fillable = [
        'name',
        'category',
        'date',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function records()
    {
        return $this->hasMany(BusinessJournalRecord::class, 'business_journal_id');
    }
}
