<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherType extends Model
{
    use HasFactory;

    protected $table = 'voucher_types';

    protected $fillable = [
        'name',   // e.g. Sale, Refund, Adjustment
        'code',   // e.g. SALE, REFUND
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'voucher_type_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper / Constants (Recommended)
    |--------------------------------------------------------------------------
    */

    // You can use these instead of magic numbers
    public const SALE   = 'SALE';
    public const REFUND = 'REFUND';
    public const ADJUST = 'ADJUST';
    public const PAYMENT = 'PAYMENT';
    public const PURCHASE = 'PURCHASE';
    public const EXPENSE = 'EXPENSE';
    public const TRANSFER = 'TRANSFER';
    public const OPENING = 'OPENING';
    public const SALE_RETURN = 'SALE_RETURN';
    public const B_JOURNAL   = 'B_JOURNAL';

    /**
     * Resolve voucher type id by code
     */
    public static function idByCode(string $code): ?int
    {
        return static::where('code', $code)->value('id');
    }
}
