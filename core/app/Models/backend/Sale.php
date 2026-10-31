<?php
namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'invoice_no',
        'branch_id',
        'warehouse_id',
        'customer_id',
        'name',
        'phone',
        'user_id',
        'coupon_id',
        'pos_session_id',
        'sale_type',
        'source',
        'status',
        'subtotal',
        'discount',
        'coupon_code',
        'coupon_discount',
        'point_discount',
        'tax_amount',
        'shipping_charge',
        'shipping_address',
        'postal_code',
        'city',
        'total',
        'paid_amount',
        'due_amount',
        'payment_status',
        'sale_note',
        'created_at',
        'updated_at',
        'transaction_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ================= Relations ================= */

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function journal()
    {
        return $this->hasOne(Journal::class, 'ref_id')->where('ref_type', 'sale');
    }

    /* ================= Helpers ================= */

    public function recalcPaymentStatus(): void
    {
        if ($this->paid_amount <= 0) {
            $this->payment_status = 'due';
        } elseif ($this->paid_amount < $this->total) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'paid';
        }
    }
}
