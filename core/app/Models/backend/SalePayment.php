<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    protected $table = 'sale_payments';

    protected $fillable = [
        'sale_id',
        'payment_type_id',
        'account_id',
        'payment_type',
        'amount',
        'change_amount',
        'reference',
        'paid_at',
        'received_by',
        'note',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    /* ================= Relations ================= */

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    /* ================= Helpers ================= */

    public function resolvedPaymentType(): string
    {
        // Prefer FK-based type
        if ($this->paymentType) {
            return $this->paymentType->name;
        }

        // Fallback ENUM value
        return $this->payment_type ?? 'cash';
    }
}
