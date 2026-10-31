<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'journal_entries';

    protected $fillable = [
        'voucher_no',
        'voucher_type_id',
        'branch_id',
        'fiscal_year_id',
        'source_id',
        'source_ref_id',
        'entry_date',
        'narration',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    // --------------------
    // Relationships
    // --------------------

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voucherType()
    {
        return $this->belongsTo(VoucherType::class, 'voucher_type_id');
    }
}
