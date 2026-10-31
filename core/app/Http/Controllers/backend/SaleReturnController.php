<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\BranchAccount;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\PaymentType;
use App\Models\backend\Sale;
use App\Models\backend\SaleItem;
use App\Models\backend\SaleReturn;
use App\Models\backend\SaleReturnItem;
use App\Models\backend\SaleReturnPayment;
use App\Models\backend\VoucherType;
use App\Services\StockLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleReturnController extends Controller
{
    public function index()
    {
        return view('backend.modules.sale_returns.index');
    }

    /**
     * Show sale return page
     */
    public function create($saleId)
    {
        $user = auth()->user();

        /* ---------------------------------
        | 1️⃣ Load Sale (with safety)
        ---------------------------------*/
        $sale = Sale::with([
            'items.product',
            'customer',
        ])
            ->where('id', $saleId)
            ->where('status', 'delivered')
            ->firstOrFail();

        // 🔐 branch guard
        if (! $user->isSuperAdmin() && $sale->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access');
        }

        /* ---------------------------------
        | 2️⃣ Sale Items
        ---------------------------------*/
        $saleItems = $sale->items;

        /* ---------------------------------
        | 3️⃣ Already Returned Qty Map
        | sale_item_id => total_returned_qty
        ---------------------------------*/
        $returnedQtyMap = SaleReturnItem::whereIn(
            'sale_item_id',
            $saleItems->pluck('id')
        )
            ->selectRaw('sale_item_id, SUM(qty) as total_qty')
            ->groupBy('sale_item_id')
            ->pluck('total_qty', 'sale_item_id')
            ->toArray();

        /* ---------------------------------
        | 4️⃣ Payment Types (for refund)
        ---------------------------------*/
        $paymentTypes = PaymentType::where('is_active', 1)
            ->orderBy('name')
            ->get();

        /* ---------------------------------
        | 5️⃣ Render View
        ---------------------------------*/
        return view(
            'backend.modules.sale_returns.create',
            compact(
                'sale',
                'saleItems',
                'returnedQtyMap',
                'paymentTypes'
            )
        );
    }

    /**
     * Store sale return
     */

    public function store(Request $request, $saleId)
    {
        $data = $request->validate([
            'items'                   => 'required|array|min:1',
            'items.*.sale_item_id'    => 'required|exists:sale_items,id',
            'items.*.qty'             => 'nullable|numeric|min:0',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'payment_type_id'         => 'nullable|exists:payment_types,id',
            'refund_paid'             => 'nullable|numeric|min:0',
            'notes'                   => 'nullable|string|max:500',
        ]);

        // 🔒 Super Admin must select a branch from header
        if (auth()->user()->isSuper() && ! current_branch_id()) {
            return redirect()->back()
                ->withErrors('Please select a branch  before creating a sale return.');
        }

        $sale = Sale::with(['items', 'customer'])->findOrFail($saleId);

        $itemsToReturn = collect($data['items'])
            ->map(function ($row) {
                $row['qty'] = (float) ($row['qty'] ?? 0);
                return $row;
            })
            ->filter(fn($row) => $row['qty'] > 0)
            ->values();

        if ($itemsToReturn->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Please enter return quantity for at least one item.',
            ]);
        }

        if ($sale->status !== 'delivered') {
            throw ValidationException::withMessages([
                'sale' => 'Only delivered sales can be returned.',
            ]);
        }

        $branchId    = current_branch_id();
        $warehouseId = current_warehouse_id();
        $userId      = auth()->id();

        return DB::transaction(function () use (
            $sale,
            $data,
            $itemsToReturn,
            $branchId,
            $warehouseId,
            $userId
        ) {

            /* ---------------------------------------------------
            | 1️⃣ Create Sale Return master
            --------------------------------------------------- */
            $saleReturn = SaleReturn::create([
                'sale_id'      => $sale->id,
                'customer_id'  => $sale->customer_id,
                'branch_id'    => $branchId,
                'warehouse_id' => $warehouseId,
                'return_date'  => now()->toDateString(),
                'status'       => 'pending',
                'notes'        => $data['notes'] ?? null,
                'created_by'   => $userId,
            ]);

            $totalRefund = 0;
            $stockItems  = [];

            /* ---------------------------------------------------
            | 2️⃣ Sale Return Items
            --------------------------------------------------- */
            foreach ($itemsToReturn as $row) {

                $saleItem = SaleItem::findOrFail($row['sale_item_id']);

                // 🔒 Guard: cannot return more than sold
                if ((int) $saleItem->sale_id !== (int) $sale->id) {
                    throw ValidationException::withMessages([
                        'items' => 'Invalid sale item selected for this sale return.',
                    ]);
                }

                $alreadyReturnedQty = (float) SaleReturnItem::where('sale_item_id', $saleItem->id)->sum('qty');
                $returnableQty      = (float) $saleItem->quantity - $alreadyReturnedQty;

                if ($returnableQty <= 0) {
                    throw ValidationException::withMessages([
                        "items.{$saleItem->id}" => 'This item has no returnable quantity remaining.',
                    ]);
                }

                if ($row['qty'] > $returnableQty) {
                    throw ValidationException::withMessages([
                        "items.{$saleItem->id}" =>
                        'Return quantity exceeds available returnable quantity.',
                    ]);
                }

                $qty      = (float) $row['qty'];
                $price    = (float) $row['unit_price'];
                $discount = (float) ($row['discount_amount'] ?? 0);

                $subtotal = $qty * $price;
                if ($discount > $subtotal) {
                    $discount = $subtotal;
                }

                $refundAmount = $subtotal - $discount;

                SaleReturnItem::create([
                    'sale_return_id'  => $saleReturn->id,
                    'sale_item_id'    => $saleItem->id,
                    'product_id'      => $saleItem->product_id,
                    'qty'             => $row['qty'],
                    'unit_price'      => $row['unit_price'],
                    'refund_amount'   => $refundAmount,
                    'discount_amount' => $discount,
                ]);

                $totalRefund += $refundAmount;

                // prepare for stock IN
                $stockItems[]  = [
                    'product_id' => $saleItem->product_id,
                    'quantity'   => $row['qty'],
                    'unit_price' => $row['unit_price'],
                ];
            }

            /* ---------------------------------------------------
            | 3️⃣ Update total_refund
            --------------------------------------------------- */
            $saleReturn->update([
                'total_refund' => $totalRefund,
            ]);

            /* ---------------------------------------------------
            | 4️⃣ Stock IN (SALE RETURN)
            --------------------------------------------------- */
            StockLedgerService::increaseForSaleReturn([
                'sale_return_id' => $saleReturn->id,
                'branch_id'      => $branchId,
                'warehouse_id'   => $warehouseId,
                'user_id'        => $userId,
                'items'          => $stockItems,
            ]);

            /* ---------------------------------------------------
            | 5️⃣ Refund Payment (optional)
            --------------------------------------------------- */
            // if (! empty($data['refund_paid']) && $data['refund_paid'] > 0) {

            //     if ($data['refund_paid'] > $totalRefund) {
            //         throw ValidationException::withMessages([
            //             'refund_paid' => 'Refund cannot exceed return amount.',
            //         ]);
            //     }

            //     SaleReturnPayment::create([
            //         'sale_return_id'  => $saleReturn->id,
            //         'branch_id'       => $branchId,
            //         'account_id'      => BranchAccount::where('branch_id', $branchId)
            //             ->where('is_default', 1)
            //             ->value('account_id'),
            //         'payment_type_id' => $data['payment_type_id'],
            //         'amount'          => $data['refund_paid'],
            //         'payment_date'    => now(),
            //         'created_by'      => $userId,
            //     ]);
            // }

            // /* ---------------------------------------------------
            // | 6️⃣ Update Sale Return Status (AUTO)
            // --------------------------------------------------- */

            // $paidRefund = SaleReturnPayment::where('sale_return_id', $saleReturn->id)
            //     ->sum('amount');

            // if ($paidRefund <= 0) {
            //     $status = 'pending';
            // } elseif ($paidRefund < $totalRefund) {
            //     $status = 'partial';
            // } else {
            //     $status = 'refunded';
            // }

            // $saleReturn->update([
            //     'status' => $status,
            // ]);

            return redirect()
                ->route('pos.saleReturns.index')
                ->with('success', 'Sale return created successfully');

            // return response()->json([
            //     'success'        => true,
            //     'sale_return_id' => $saleReturn->id,
            //     'refund_amount' => number_format($totalRefund, 2),
            // ], 201);
        });
    }

    /**
     * Sale Return List Ajax
     */
    public function listAjax(Request $request)
    {
        $columns = [
            'id',
            'reference_no',
            'sale_id',
            'customer_name',
            'return_date',
            'total_refund',
            'total_refunded',
            'total_due',
            'status',
            'created_at',
        ];

        $draw     = (int) $request->input('draw');
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->input('search.value', ''));

        $user = auth()->user();

        $base = SaleReturn::with(['sale', 'customer'])
            ->select([
                'sale_returns.id',
                'sale_returns.reference_no',
                'sale_returns.sale_id',
                'sale_returns.customer_id',
                'sale_returns.branch_id',
                'sale_returns.return_date',
                'sale_returns.total_refund',
                'sale_returns.status',
                'sale_returns.created_at',
            ]);

        /* 🔐 Branch guard */
        if (! $user->isSuper()) {
            $base->where('sale_returns.branch_id', $user->branch_id);
        } elseif (current_branch_id()) {
            $base->where('sale_returns.branch_id', current_branch_id());
        }

        $total = (clone $base)->count();

        /* 🔍 Search */
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('sale_returns.reference_no', 'like', "%{$search}%")
                    ->orWhereHas('sale', fn($s) =>
                        $s->where('invoice_no', 'like', "%{$search}%")
                    )
                    ->orWhereHas('customer', fn($c) =>
                        $c->where('name', 'like', "%{$search}%")
                    );
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'sale_returns.id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];

        foreach ($rows as $r) {

            $refunded = $r->payments->sum('amount');
            $due      = round($r->total_refund - $refunded, 2);

            $statusBadge = match ($r->status) {
                'pending'  => '<span class="badge text-sm fw-semibold rounded-pill bg-danger-600 px-20 py-9 radius-4 text-white">Pending</span>',
                'partial'  => '<span class="badge text-sm fw-semibold rounded-pill bg-warning-600 px-20 py-9 radius-4 text-white">Partial</span>',
                'refunded' => '<span class="badge text-sm fw-semibold rounded-pill bg-success-600 px-20 py-9 radius-4 text-white">Refunded</span>',
                default    => '<span class="badge text-sm fw-semibold rounded-pill bg-secondary text-white">' . e($r->status) . '</span>',
            };

            /* 👁 View button (always) */
            $actions = '
                    <a href="#"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                            bg-info-focus text-info-main AjaxViewModal"
                    title="View Sale Return"
                    data-size="lg"
                    data-ajax-modal="' . route('pos.saleReturns.show', $r->id) . '">
                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                    </a>
                ';

            /* 💰 Add Payment button (ONLY if due > 0) */
            if ($due > 0) {
                $actions .= '
                    <a href="#"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                            bg-warning-focus text-warning-main AjaxModal"
                    title="Add Refund Payment"
                    data-size="md"
                    data-ajax-modal="' . route('pos.saleReturns.payment.modal', $r->id) . '"
                    data-onsuccess="saleReturnPayIndex.onSaved">
                        <iconify-icon icon="material-symbols:currency-exchange-rounded" class="text-lg"></iconify-icon>
                    </a>
                    ';
            }

            $data[] = [
                $r->id,
                e($r->reference_no ?? '-'),
                '<strong>' . e($r->sale?->invoice_no ?? '-') . '</strong>',
                e($r->customer?->name ?? 'Walk In'),
                $r->return_date,
                number_format($r->total_refund, 2),
                number_format($refunded, 2),
                number_format($due, 2),
                $statusBadge,
                $r->created_at->format('Y-m-d H:i'),
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

    /**
     * Show Sale Return details
     */
    public function show($id)
    {
        $saleReturn = SaleReturn::with([
            'sale',
            'customer',
            'items.product',
            'payments.paymentType',
        ])->findOrFail($id);

        return view('backend.modules.sale_returns.show_modal', compact('saleReturn'));
    }

    /**
     * Show Sale Return Payment Modal
     */
    public function paymentModal(SaleReturn $saleReturn)
    {
        $saleReturn->load(['payments']);

        $paid = $saleReturn->payments->sum('amount');
        $due  = $saleReturn->total_refund - $paid;

        if ($due <= 0) {
            abort(422, 'No due refund remaining.');
        }

        $paymentTypes = PaymentType::where('is_active', 1)->get();

        return view(
            'backend.modules.sale_returns.payment_modal',
            compact('saleReturn', 'paid', 'due', 'paymentTypes')
        );
    }

    /**
     * Store Sale Return Payment
     */
    public function storePayment(Request $request, SaleReturn $saleReturn)
    {
        $data = $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'amount'          => 'required|numeric|min:0.01',
            'notes'           => 'nullable|string|max:255',
        ]);

        // 🔒 Safety: must belong to current branch
        $branchId = current_branch_id();
        if (! $branchId || $saleReturn->branch_id != $branchId) {
            abort(403, 'Invalid branch context.');
        }

        // 💰 Calculate due
        $alreadyPaid = SaleReturnPayment::where('sale_return_id', $saleReturn->id)
            ->sum('amount');

        $due = round($saleReturn->total_refund - $alreadyPaid, 2);

        if ($due <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'This return is already fully refunded.',
            ]);
        }

        if ($data['amount'] > $due) {
            throw ValidationException::withMessages([
                'amount' => 'Refund amount cannot exceed due refund.',
            ]);
        }

        return DB::transaction(function () use (
            $saleReturn,
            $data,
            $branchId,
            $alreadyPaid,
            $due
        ) {

            /* ---------------------------------------------------
            | 1️⃣ Get default cash/bank account
            --------------------------------------------------- */
            $accountId = BranchAccount::where('branch_id', $branchId)
                ->where('is_default', 1)
                ->value('account_id');

            if (! $accountId) {
                throw new \Exception('Default branch account not configured.');
            }

            /* ---------------------------------------------------
            | 2️⃣ Store refund payment
            --------------------------------------------------- */
            SaleReturnPayment::create([
                'sale_return_id'  => $saleReturn->id,
                'branch_id'       => $branchId,
                'account_id'      => $accountId,
                'payment_type_id' => $data['payment_type_id'],
                'amount'          => $data['amount'],
                'payment_date'    => now(),
                'notes'           => $data['notes'] ?? null,
                'created_by'      => auth()->id(),
            ]);

            /* ---------------------------------------------------
            | 3️⃣ Update Sale Return status
            --------------------------------------------------- */
            // $newPaid = $alreadyPaid + $data['amount'];

            // if ($newPaid >= $saleReturn->total_refund) {
            //     $saleReturn->status = 'refunded';
            // } else {
            //     $saleReturn->status = 'partial';
            // }

            // $saleReturn->save();

            $paidRefund = SaleReturnPayment::where('sale_return_id', $saleReturn->id)
                ->sum('amount');

            if ($paidRefund <= 0) {
                $status = 'pending';
            } elseif ($paidRefund < $saleReturn->total_refund) {
                $status = 'partial';
            } else {
                $status = 'refunded';
            }

            $saleReturn->update(['status' => $status]);

            /* ---------------------------------------------------
            | 4️⃣ (Optional) Journal Entry — SINGLE ENTRY STYLE
            --------------------------------------------------- */

            $journal = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('SALE_RETURN'),
                'voucher_type_id' => VoucherType::idByCode('SALE_RETURN'),
                'branch_id'       => $branchId,
                'fiscal_year_id'  => currentFiscalYear()->id,
                'source_id'       => $saleReturn->id,
                'entry_date'      => now()->toDateString(),
                'narration'       => 'Sale Return Refund #' . $saleReturn->id,
                'created_by'      => auth()->id(),
            ]);

            if (! isset($journal)) {
                throw new \Exception('Journal entry not created.');
            }

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $accountId,
                'branch_id'        => $branchId,
                'debit'            => 0,
                'credit'           => $data['amount'], 
            ]);

            return response()->json([
                'success' => true,
                'msg'     => 'Refund payment saved successfully',
            ], 201);
        });
    }

}
