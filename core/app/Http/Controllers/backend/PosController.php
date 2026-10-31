<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Branch;
use App\Models\backend\BranchAccount;
use App\Models\backend\Category;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\LoyaltyRule;
use App\Models\backend\LoyaltyTransaction;
use App\Models\backend\PaymentType;
use App\Models\backend\Product;
use App\Models\backend\Sale;
use App\Models\backend\SaleItem;
use App\Models\backend\SalePayment;
use App\Models\backend\StockCurrent;
use App\Models\backend\StockLedger;
use App\Models\backend\VoucherType;
use App\Models\backend\Warehouse;
use App\Services\LoyaltyPointService;
use App\Services\StockLedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $branchId = auth()->user()->branch_id;

        $paymentTypes = PaymentType::where('is_active', 1)->get();

        return view('backend.modules.pos.index', compact(['categories', 'paymentTypes']));
    }

    /**
     * Store POS Sale
     */

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',

            'sale_type'          => 'required|string',
            'source'             => 'nullable|string',
            'status'             => 'required|in:delivered,hold,draft',

            'subtotal'           => 'required|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'coupon_id'          => 'nullable|exists:coupons,id',
            'coupon_code'        => 'nullable|string',
            'coupon_discount'    => 'nullable|numeric|min:0',
            'redeem_point'       => 'nullable|boolean',
            'shipping_charge'    => 'nullable|numeric|min:0',
            'total'              => 'required|numeric|min:0',

            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',

            'payments'           => 'nullable|array',
            'payments.*.method'  => 'required|string',
            'payments.*.amount'  => 'required|numeric|min:0.01',
        ]);

        // return $data;


        /* -----------------------------
        | 🔐 SECURITY: recalc total
        -----------------------------*/
        $subtotal = collect($data['items'])
            ->sum(fn($i) => $i['quantity'] * Product::find($i['product_id'])->price);

        $redeemPointDiscount = 0;
        if (! empty($data['redeem_point']) && ! empty($data['customer_id'])) {
            $redeemPointDiscount = (int) LoyaltyPointService::redeemPoints($data['customer_id']);
        }

        $calculatedTotal = round(
            $subtotal
                - ($data['discount'] ?? 0)
                - ($data['coupon_discount'] ?? 0)
                - $redeemPointDiscount
                + ($data['shipping_charge'] ?? 0),
            2
        );

        if (round($data['total'], 2) !== $calculatedTotal) {
            throw ValidationException::withMessages([
                'total' => 'Total mismatch. Please refresh POS.',
            ]);
        }

        /* -----------------------------
        | 💰 PAYMENT CALCULATION (GLOBAL)
        -----------------------------*/
        $payments = collect($data['payments'] ?? []);

        $paidAmount = round(
            $payments->sum(fn($p) => (float) $p['amount']),
            2
        );

        // overpayment protection
        if ($paidAmount > $calculatedTotal) {
            $paidAmount = $calculatedTotal;
        }

        $dueAmount = round($calculatedTotal - $paidAmount, 2);

        if ($paidAmount <= 0) {
            $paymentStatus = 'due';
        } elseif ($paidAmount < $calculatedTotal) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'paid';
        }

        /* -----------------------------
        | 🌿 Branch & Warehouse Context
        -----------------------------*/
        $branchId    = current_branch_id();
        $warehouseId = current_warehouse_id();

        abort_if(
            ! $branchId || ! $warehouseId,
            422,
            'Branch/Warehouse context missing.'
        );

        /* -----------------------------
        | 🚀 TRANSACTION
        -----------------------------*/
        try {
            return DB::transaction(function () use (
                $request,
                $data,
                $subtotal,
                $calculatedTotal,
                $redeemPointDiscount,
                $branchId,
                $warehouseId,
                $payments,
                $paidAmount,
                $dueAmount,
                $paymentStatus
            ) {

                /* -----------------------------
                | 🧾 SALE CREATE / RESUME
                -----------------------------*/
                if ($request->resume_sale_id) {

                    $sale = Sale::where('id', $request->resume_sale_id)
                        ->where('status', 'hold')
                        ->lockForUpdate()
                        ->firstOrFail();

                    $sale->update([
                        'status'          => 'delivered',
                        'subtotal'        => $subtotal,
                        'discount'        => $data['discount'] ?? 0,
                        'coupon_discount' => $data['coupon_discount'] ?? 0,
                        'point_discount'  => $redeemPointDiscount,
                        'shipping_charge' => $data['shipping_charge'] ?? 0,
                        'total'           => $calculatedTotal,

                        'paid_amount'     => $paidAmount,
                        'due_amount'      => $dueAmount,
                        'payment_status'  => $paymentStatus,
                    ]);

                    $sale->items()->delete();
                    $sale->payments()->delete();
                } else {

                    $sale = Sale::create([
                        'invoice_no'      => 'POS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                        'branch_id'       => $branchId,
                        'warehouse_id'    => $warehouseId,
                        'customer_id'     => $data['customer_id'] ?? null,
                        'user_id'         => auth()->id(),

                        'sale_type'       => $data['sale_type'],
                        'source'          => $data['source'], 
                        'status'          => $data['status'],

                        'subtotal'        => $subtotal,
                        'discount'        => $data['discount'] ?? 0,
                        'coupon_id'       => $data['coupon_id'] ?? null,
                        'coupon_code'     => $data['coupon_code'] ?? null,
                        'coupon_discount' => $data['coupon_discount'] ?? 0,
                        'point_discount'  => $redeemPointDiscount,
                        'shipping_charge' => $data['shipping_charge'] ?? 0,
                        'total'           => $calculatedTotal,

                        'paid_amount'     => $paidAmount,
                        'due_amount'      => $dueAmount,
                        'payment_status'  => $paymentStatus,

                        'sale_note'       => $data['sale_note'] ?? null,
                    ]);

                    if ($redeemPointDiscount > 0) {

                        LoyaltyTransaction::create([
                            'customer_id' => $data['customer_id'] ?? null,
                            'sale_id'     => $sale->id,
                            'points'      => (-1) * LoyaltyPointService::calculateRedeemablePoints($data['customer_id'] ?? null),
                            'type'        => 'redeem',
                            'description' => 'Points redeemed for Sale #' . $sale->invoice_no,
                        ]);
                    }

                    $loyalty_rule = LoyaltyRule::where('is_active', 1)->first();
                    if ($loyalty_rule) {
                        $earned_points = floor($subtotal / $loyalty_rule->earn_amount) * $loyalty_rule->earn_points;
                        if ($earned_points > 0) {
                            LoyaltyTransaction::create([
                                'customer_id' => $data['customer_id'] ?? null,
                                'sale_id'     => $sale->id,
                                'points'      => $earned_points,
                                'type'        => 'earn',
                                'description' => 'Points earned from Sale #' . $sale->invoice_no,
                            ]);
                        }
                    }
                }

                /* -----------------------------
                | 📦 ITEMS
                -----------------------------*/
                foreach ($data['items'] as $item) {
                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'line_total' => round($item['quantity'] * $item['unit_price'], 2),
                    ]);
                }

                /* -----------------------------
                | 📉 STOCK (only delivered)
                -----------------------------*/
                if ($sale->status === 'delivered') {
                    StockLedgerService::deductForSale([
                        'sale_id'      => $sale->id,
                        'warehouse_id' => $warehouseId,
                        'branch_id'    => $branchId,
                        'user_id'      => auth()->id(),
                        'items'        => collect($data['items'])->map(fn($row) => [
                            'product_id' => $row['product_id'],
                            'quantity'   => $row['quantity'],
                            'unit_price' => $row['unit_price'],
                        ])->toArray(),
                    ]);
                }

                /* -----------------------------
                | 💵 PAYMENTS + JOURNAL
                -----------------------------*/
                if ($paidAmount > 0) {

                    $cashAccountId = BranchAccount::where('branch_id', $branchId)
                        ->where('is_default', 1)
                        ->value('account_id');

                    if (! $cashAccountId) {
                        throw new \Exception('Cash account not configured for branch.');
                    }

                    foreach ($payments as $pay) {
                        $type = PaymentType::findOrFail($pay['method']);

                        SalePayment::create([
                            'sale_id'         => $sale->id,
                            'account_id'      => $cashAccountId,
                            'payment_type_id' => $type->id,
                            'payment_type'    => $type->slug,
                            'amount'          => $pay['amount'],
                            'received_by'     => auth()->user()->name,
                            'paid_at'         => now(),
                        ]);
                    }

                    // $revenueAccountId = config('accounting.sales_revenue_account_id');

                    $journal = JournalEntry::create([
                        'voucher_no'      => generateVoucherNo('SALE'),
                        'voucher_type_id' => VoucherType::idByCode('SALE'),
                        'branch_id'       => $branchId,
                        'fiscal_year_id'  => currentFiscalYear()->id,
                        'source_id'       => $sale->id,
                        'entry_date'      => now()->toDateString(),
                        'narration'       => 'POS Sale #' . $sale->invoice_no,
                        'created_by'      => auth()->id(),
                    ]);

                    JournalEntryLine::create([
                        'journal_entry_id' => $journal->id,
                        'account_id'       => $cashAccountId,
                        'branch_id'        => $branchId,
                        'debit'            => $paidAmount,
                        'credit'           => 0,
                    ]);

                    // JournalEntryLine::create([
                    //     'journal_entry_id' => $journal->id,
                    //     'account_id'       => $revenueAccountId,
                    //     'branch_id'        => $branchId,
                    //     'debit'            => 0,
                    //     'credit'           => $paidAmount,
                    // ]);
                }

                return response()->json([
                    'success' => true,
                    'id'      => $sale->id,
                    'invoice' => $sale->invoice_no,
                ]);
            });
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }
    //store ends

    /**
     * Generate Invoice Number
     */
    protected function generateInvoiceNo(): string
    {
        return 'POS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }

    public function hold(Request $request)
    {
        $data = $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'warehouse_id'       => 'required|exists:warehouses,id',
            'customer_id'        => 'nullable|exists:customers,id',

            'subtotal'           => 'required|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'tax_amount'         => 'nullable|numeric|min:0',
            'shipping_charge'    => 'nullable|numeric|min:0',
            'total'              => 'required|numeric|min:0',

            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {

            $sale = Sale::create([
                'invoice_no'      => 'HOLD-' . now()->format('Ymd-His'),
                'branch_id'       => $data['branch_id'],
                'warehouse_id'    => $data['warehouse_id'],
                'customer_id'     => $data['customer_id'] ?? null,
                'user_id'         => auth()->id(),
                'status'          => 'hold',
                'sale_type'       => 'retail',
                'source'          => $data['source'] ?? null,

                'subtotal'        => $data['subtotal'],
                'discount'        => $data['discount'] ?? 0,
                'tax_amount'      => $data['tax_amount'] ?? 0,
                'shipping_charge' => $data['shipping_charge'] ?? 0,
                'total'           => $data['total'],

                'paid_amount'     => 0,
                'due_amount'      => $data['total'],
                'payment_status'  => 'due',
            ]);

            foreach ($data['items'] as $row) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $row['product_id'],
                    'quantity'   => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'line_total' => ($row['unit_price'] * $row['quantity']),
                ]);
            }

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'Sale put on hold',
            ]);
        });
    }

    //*** Fetch Hold Sales ***

    public function holdList()
    {
        $user = auth()->user();

        $query = Sale::with(['customer', 'items'])
            ->where('status', 'hold');

        // -----------------------------
        // 🔐 Branch-wise access control
        // -----------------------------
        if (! $user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $sales = $query
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $sales->map(function ($sale) {
                return [
                    'id'         => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'customer'   => optional($sale->customer)->name ?? 'Walk In',
                    'quantity'   => $sale->items->sum('quantity'),
                    'unit_price' => $sale->items->first()?->unit_price,
                    'total'      => $sale->total,
                ];
            }),
        ]);
    }

    //*** Resume Hold Sale ***
    // public function resume(Sale $sale)
    // {
    //     if ($sale->status !== 'hold') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'This sale is not on hold',
    //         ], 422);
    //     }

    //     $sale->load('items.product', 'customer:id,name');

    //     return response()->json([
    //         'success' => true,
    //         'sale'    => $sale,
    //     ]);
    // }

    // PosController.php

    public function resume(Sale $sale)
    {
        // 🔐 Branch security
        if (
            ! auth()->user()->isSuperAdmin() &&
            $sale->branch_id !== auth()->user()->branch_id
        ) {
            abort(403);
        }

        // 🔒 Only HOLD sale editable
        if ($sale->status !== 'hold') {
            abort(403, 'Only HOLD sales can be edited');
        }

        $branchId = current_branch_id();

        $sale->load([
            'items.product' => function ($query) use ($branchId) {
                $query->leftJoin('stock_currents', function ($join) use ($branchId) {
                    $join->on('products.id', '=', 'stock_currents.product_id')
                        ->where('stock_currents.branch_id', '=', $branchId);
                })
                    ->select(
                        'products.*',
                        \DB::raw('SUM(stock_currents.quantity) as stock')
                    )
                    ->groupBy('products.id');
            },
            'customer',
        ]);

        return response()->json([
            'success' => true,
            'sale'    => [
                'id'              => $sale->id,
                'customer_id'     => $sale->customer_id,
                'customer_name'   => $sale->customer?->name ?? 'Walk In',
                'subtotal'        => $sale->subtotal,
                'discount'        => $sale->discount,
                'shipping_charge' => $sale->shipping_charge,

                'items'           => $sale->items->map(function ($i) {
                    return [
                        'product_id' => $i->product_id,
                        'quantity'   => $i->quantity,
                        'unit_price' => $i->unit_price,
                        'product'    => [
                            'name'      => $i->product->name,
                            'mrp'       => $i->product->mrp,
                            'parent_id' => $i->product->parent_id,
                            'stock'     => $i->product->stock ?? 0,
                        ],
                    ];
                }),
            ],
        ]);
    }

    //*** Fetch Today's Orders ***

    public function todayOrders()
    {
        $user = auth()->user();

        $start = Carbon::today()->startOfDay();
        $end   = Carbon::today()->endOfDay();

        $query = Sale::with('customer')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['delivered', 'hold']);

        // -----------------------------
        // 🔐 Branch-wise access control
        // -----------------------------
        if (! $user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $sales = $query
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($sale) {
                return [
                    'id'       => $sale->id,
                    'invoice'  => $sale->invoice_no,
                    'customer' => $sale->customer?->name ?? 'Walk In',
                    'total'    => $sale->total,
                    'status'   => $sale->status,
                    'time'     => $sale->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'sales'   => $sales,
        ]);
    }

    //*** Void a Sale ***
    public function void(Sale $sale, Request $request)
    {
        // safety checks
        if (! in_array($sale->status, ['delivered', 'hold'])) {
            return response()->json([
                'success' => false,
                'message' => 'This sale cannot be voided',
            ], 422);
        }

        DB::transaction(function () use ($sale, $request) {

            // 1️⃣ Reverse stock
            foreach ($sale->items as $item) {
                StockLedger::create([
                    'txn_date'     => now(),
                    'product_id'   => $item->product_id,
                    'warehouse_id' => $sale->warehouse_id,
                    'branch_id'    => $sale->branch_id,
                    'ref_type'     => 'SALE_VOID',
                    'ref_id'       => $sale->id,
                    'direction'    => 'IN',
                    'quantity'     => $item->quantity,
                    'unit_cost'    => $item->unit_price,
                    'note'         => 'Sale voided',
                    'created_by'   => auth()->id(),
                ]);

                // update stock_currents
                StockCurrent::where('product_id', $item->product_id)
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->where('branch_id', $sale->branch_id)
                    ->increment('quantity', $item->quantity);
            }

            // 2️⃣ Update sale
            $sale->update([
                'status'         => 'void',
                'payment_status' => 'Refund',
                'sale_note'      => trim(($sale->sale_note ?? '') . ' | VOID'),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Sale voided successfully',
        ]);
    }

    //*** Show Sale Details ***

    // public function show(Sale $sale)
    // {
    //     $sale->load([
    //         'customer',
    //         'items.product',
    //         'payments.paymentType',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'sale'    => [
    //             'invoice'  => $sale->invoice_no,
    //             'customer' => $sale->customer?->name ?? 'Walk In',
    //             'status'   => $sale->status,
    //             'subtotal' => $sale->subtotal,
    //             'discount' => $sale->discount,
    //             'shipping' => $sale->shipping_charge,
    //             'total'    => $sale->total,
    //             'paid'     => $sale->paid_amount,
    //             'items'    => $sale->items->map(fn($i) => [
    //                 'name'  => $i->product->name,
    //                 'qty'   => $i->quantity,
    //                 'price' => $i->unit_price,
    //                 'total' => $i->line_total,
    //             ]),
    //             'payments' => $sale->payments->map(fn($p) => [
    //                 'method' => $p->payment_type,
    //                 'amount' => $p->amount,
    //             ]),
    //         ],
    //     ]);
    // }

    public function show(Sale $sale)
    {

        if (
            ! auth()->user()->isSuperAdmin() &&
            $sale->branch_id !== auth()->user()->branch_id
        ) {
            abort(403);
        }

        $sale->load([
            'customer',
            'items.product',
            'payments.paymentType',
            'user',
        ]);

        return view('backend.modules.pos.saleDetailsModal', compact('sale'));
    }

    //*** Generate Invoice ***
    public function invoice(Sale $sale)
    {
        $sale->load([
            'items.product',
            'payments',
            'customer',
            'branch',
            'warehouse',
        ]);

        return view('backend.modules.pos.invoice', compact('sale'));
    }

    //*** Fetch Today's Transactions ***

    public function todayTransactions()
    {
        $user = auth()->user();

        $query = SalePayment::with(['sale.user'])
            ->whereDate('paid_at', Carbon::today());

        // -----------------------------------
        // 🔐 Branch-wise access control
        // -----------------------------------
        if (! $user->isSuperAdmin()) {
            $query->whereHas('sale', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        //  Exclude void / refunded sales
        $query->whereHas('sale', function ($q) {
            $q->where('status', 'delivered'); // only completed sales
        });

        // Fetch + Format
        $transactions = $query
            ->orderByDesc('paid_at')
            ->get()
            ->map(function ($p) {
                return [
                    'invoice' => $p->sale?->invoice_no ?? '-',
                    'method'  => strtolower($p->payment_type), // cash/card/bkash
                    'amount'  => (float) $p->amount,
                    'time'    => optional($p->paid_at)->format('H:i'),
                    'user'    => $p->sale?->user?->name ?? 'System',
                    'branch'  => $p->sale?->branch?->name ?? '-',
                ];
            });

        return response()->json([
            'success'      => true,
            'transactions' => $transactions,
        ]);
    }

    //*** Fetch Product by Barcode ***
    public function productByBarcode(Request $request)
    {
        $branchId = current_branch_id();
        $product = Product::query()
            ->leftJoin('stock_currents', function ($join) use ($branchId) {
                $join->on('products.id', '=', 'stock_currents.product_id')
                    ->where('stock_currents.branch_id', '=', $branchId);
            })
            ->where('products.barcode', $request->barcode)
            ->where('products.is_sellable', 1)
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.mrp',
                'products.unit_id',
                \DB::raw('SUM(stock_currents.quantity) as stock')
            )
            ->groupBy('products.id', 'products.name', 'products.price', 'products.mrp', 'products.unit_id')
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id'      => $product->id,
                'name'    => $product->name,
                'price'   => $product->price,
                'mrp'     => $product->mrp,
                'unit_id' => $product->unit_id,
                'stock'   => $product->stock ?? 0,
            ],
        ]);
    }

    //--------------------------------
    //*** Sales List View & Ajax ***
    //--------------------------------

    public function list()
    {
        return view('backend.modules.pos.list');
    }

    public function listAjax(Request $request)
    {
        $columns = [
            'id',
            'invoice_no',
            'customer_name',
            'total',
            'paid_amount',
            'due_amount',
            'status',
            'payment_status',
            'created_at',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $user = auth()->user();

        $base = Sale::with(['customer', 'user'])
            ->select([
                'sales.id',
                'sales.invoice_no',
                'sales.customer_id',
                'sales.total',
                'sales.paid_amount',
                'sales.due_amount',
                'sales.status',
                'sales.payment_status',
                'sales.created_at',
                'sales.branch_id',
                'sales.user_id',
            ]);

        // 🔐 Branch restriction
        if (! $user->isSuperAdmin()) {
            $base->where('sales.branch_id', $user->branch_id);
        }

        $total = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('sales.invoice_no', 'like', "%{$search}%")
                    ->orWhereHas(
                        'customer',
                        fn($c) =>
                        $c->where('name', 'like', "%{$search}%")
                    );
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];

        foreach ($rows as $r) {

            $statusLabel = match ($r->status) {
                'pending'   => '<span class="badge text-sm fw-semibold bg-info-600 px-20 py-9 radius-4 text-white">Pending</span>',
                'confirmed' => '<span class="badge text-sm fw-semibold bg-lilac-600 px-20 py-9 radius-4 text-white">Confirmed</span>',
                'delivered' => '<span class="badge text-sm fw-semibold bg-success-600 px-20 py-9 radius-4 text-white">Delivered</span>',
                'cancelled' => '<span class="badge text-sm fw-semibold bg-danger-600 px-20 py-9 radius-4 text-white">Cancelled</span>',
                'returned'  => '<span class="badge text-sm fw-semibold bg-warning-600 px-20 py-9 radius-4 text-white">Returned</span>',
                'hold'      => '<span class="badge text-sm fw-semibold bg-warning-600 px-20 py-9 radius-4 text-white">Hold</span>',
                'void'      => '<span class="badge text-sm fw-semibold bg-danger-600 px-20 py-9 radius-4 text-white">Void</span>',
                default     => '<span class="badge text-sm fw-semibold bg-lilac-600 px-20 py-9 radius-4 text-white">' . e(ucfirst($r->status)) . '</span>',
            };

            $statusBadge = '<a href="javascript:void(0)" class="AjaxModal" 
                            data-ajax-modal="' . route('pos.sales.status.modal', $r->id) . '" 
                            data-onsuccess="posSalePaymentIndex.onSaved"
                            data-size="sm">' . $statusLabel . '</a>';

            $payBadgeHtml = match ($r->payment_status) {
                'paid'  => '<span class="badge text-sm fw-semibold rounded-lg bg-success-600 px-20 py-9 radius-4 text-white">Paid</span>',
                'pending'   => '<span class="badge text-sm fw-semibold rounded-lg bg-info-600 px-20 py-9 radius-4 text-white">Pending</span>',
                default => '<span class="badge text-sm fw-semibold rounded-lg bg-lilac-600 px-20 py-9 radius-4 text-white">' . e(ucfirst($r->payment_status)) . '</span>',
            };

            $payBadge = '<a href="javascript:void(0)" class=""  
                            data-ajax-modal="' . route('pos.sales.payment_status.modal', $r->id) . '" 
                            data-onsuccess="posSalePaymentIndex.onSaved"
                            data-size="sm">' . $payBadgeHtml . '</a>'; // AjaxModal disabled 

            $actions = '<div class="d-inline-flex justify-content-end gap-1">';

            /* 🖨 Invoice */
            $actions .= '
                    <a href="' . route('pos.sales.invoice', $r->id) . '" target="_blank"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-info-focus text-info-main" title="Invoice">
                    <iconify-icon icon="mdi:printer-outline"></iconify-icon>
                    </a>';

            /* 💰 Receive Payment (only if due & delivered) */
            if ($r->due_amount > 0 && $r->status === 'delivered') {
                $actions .= '
                    <a href="#"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                            bg-warning-focus text-warning-main AjaxModal"
                    title="Receive Payment" data-onsuccess="posSalePaymentIndex.onSaved"
                    data-size="lg"
                    data-ajax-modal="' . route('pos.sales.payment.modal', $r->id) . '">
                        <iconify-icon icon="material-symbols:currency-exchange-rounded" class="text-lg"></iconify-icon>
                    </a>';
            }

            /* 👁 View (Ajax Modal) */
            $actions .= '
                    <a href="#"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                            bg-info-focus text-info-main AjaxViewModal"
                    title="View Sale"
                    data-size="lg"
                    data-ajax-modal="' . route('pos.sales.show', $r->id) . '">
                        <iconify-icon icon="lucide:eye"></iconify-icon>
                    </a>';

            /* ✏️ Edit / Resume (if on HOLD) */
            if ($r->status === 'hold') {
                $actions .= '
                        <a href="#"
                        class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                                bg-warning-focus text-warning-main btn-resume-sale"
                        title="Edit / Resume Sale"
                        data-id="' . $r->id . '">
                            <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                        </a>';
            }

            /* 🔁 Sale Return (only delivered) */
            if ($r->status === 'delivered') {
                $actions .= '
                    <a href="' . route('pos.sales.return.create', $r->id) . '"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                            bg-danger-focus text-danger-main"
                    title="Return Sale">
                        <iconify-icon icon="mdi:backup-restore"></iconify-icon>
                    </a>';
            }
            $actions .= '</div>';

            $data[] = [
                $r->id,
                '<strong>' . $r->invoice_no . '</strong>',
                e($r->customer?->name ?? 'Walk In'),
                number_format($r->total, 2),
                number_format($r->paid_amount, 2),
                number_format($r->due_amount, 2),
                $statusBadge,
                $payBadge,
                $r->created_at->format('Y-m-d H:i'),
                e($r->user?->name ?? 'e-commerce'),
                $actions,
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    public function statusModal(Sale $sale)
    {
        return view('backend.modules.pos.statusModal', compact('sale'));
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,cancelled,delivered',
        ]);

        if ($sale->status == 'delivered' || $sale->status == 'returned') {
            // if status already delivered or returned dont allow any furthure change.
            return response()->json([
                'ok' => false,
                'msg'     => 'Sale status cannot be changed',
            ]);
        }

        $branchId    = Branch::where('code', 'e-commerce')->value('id');
        $warehouseId = Warehouse::where('branch_id', $branchId)->where('is_default', 1)->first()?->id;
        $cashAccountId = BranchAccount::where('branch_id', $branchId)
            ->where('is_default', 1)
            ->value('account_id');


        if ($request->status == 'confirmed') {

            StockLedgerService::deductForSale([
                'sale_id'      => $sale->id,
                'warehouse_id' => $warehouseId,
                'branch_id'    => $branchId,
                'user_id'      => auth()->id(),
                'items'        => $sale->items->toArray()
            ]);
        }
 
        if ($request->status == 'cancelled' || $request->status == 'pending') {
            if ($sale->status == 'confirmed') {
                StockLedgerService::increaseForSaleReturn([
                    'sale_id'      => $sale->id,
                    'warehouse_id' => $warehouseId,
                    'branch_id'    => $branchId,
                    'user_id'      => auth()->id(),
                    'ref_type' => 'SALE_CANCELLATION',
                    'items'        => $sale->items->toArray()
                ]);
            }
        }


        if ($request->status == 'delivered') {
            // hit stock table and accounts table
            if ($sale->status == 'pending') {

                StockLedgerService::deductForSale([
                    'sale_id'      => $sale->id,
                    'warehouse_id' => $warehouseId,
                    'branch_id'    => $branchId,
                    'user_id'      => auth()->id(),
                    'ref_type'     => 'e-commerce',
                    'items'        => $sale->items->toArray()
                ]);
            }

            $salePayment = SalePayment::create([
                'sale_id' => $sale->id,
                'account_id' => $cashAccountId,
                'amount' => $sale->total,
                'payment_type' => 'cash',
                'received_by' => auth()->user()->name,
            ]);

            $journal = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('SALE'),
                'voucher_type_id' => VoucherType::idByCode('SALE'),
                'branch_id'       => $branchId,
                'fiscal_year_id'  => currentFiscalYear()->id,
                'source_id'       => $sale->id,
                'entry_date'      => now()->toDateString(),
                'narration'       => 'POS Sale #' . $sale->invoice_no,
                'created_by'      => auth()->id(),
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $cashAccountId,
                'branch_id'        => $branchId,
                'debit'            => $sale->total,
                'credit'           => 0,
            ]);

            $sale->due_amount = 0;
            $sale->paid_amount = $sale->total;

            $sale->update([
                'payment_status' => 'paid',
            ]);
        }
        $sale->status = $request->status;
        $sale->save();

        return response()->json([
            'ok' => true,
            'msg'     => 'Status updated successfully',
        ]);
    }

    public function paymentStatusModal(Sale $sale)
    {
        return view('backend.modules.pos.paymentStatusModal', compact('sale'));
    }

    public function updatePaymentStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'payment_status' => 'required|string|in:paid,pending',
        ]);

        if ($sale->payment_status == 'paid') {
            return response()->json([
                'ok' => false,
                'msg'     => 'Payment status cannot be changed to paid sale.',
            ]);
        }

        $sale->update([
            'payment_status' => $request->payment_status,
        ]);

        return response()->json([
            'ok' => true,
            'msg'     => 'Payment Status updated successfully',
        ]);
    }
}
