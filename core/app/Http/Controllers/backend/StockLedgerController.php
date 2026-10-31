<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\PurchaseReceipt;
use App\Models\backend\PurchaseReturn;
use App\Models\backend\Sale;
use App\Models\backend\SaleReturn;
use App\Models\backend\StockAdjustment;
use App\Models\backend\StockTransfer;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StockLedgerController extends Controller
{
    public function index()
    {
        return view('backend.modules.inventory.reports.stock_ledger.index');
    }

    public function listAjax(Request $request)
    {
        $productId   = $request->product_id;
        $warehouseId = $request->warehouse_id;

        if (! $productId) {
            return response()->json([
                'draw'                 => (int) $request->draw,
                'iTotalRecords'        => 0,
                'iTotalDisplayRecords' => 0,
                'aaData'               => [],
            ]);
        }

        // 🔒 Branch context (GLOBAL)
        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : null;

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : null;

        $rows = collect();

        /* ---------------------------------------------------
        | 1️⃣ Opening Balance (before from_date)
        --------------------------------------------------- */
        $openingQty = DB::table('stock_ledgers')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->when($warehouseId, fn($q) =>
                $q->where('warehouse_id', $warehouseId)
            )
            ->when($from, fn($q) =>
                $q->where('txn_date', '<', $from)
            )
            ->selectRaw("
            SUM(
                CASE
                    WHEN direction = 'IN'  THEN quantity
                    WHEN direction = 'OUT' THEN -quantity
                    ELSE 0
                END
            ) as qty
        ")
            ->value('qty') ?? 0;

        $openingQty = round($openingQty, 2);

        // Opening row
        $rows->push([
            'date'  => $from ? $from->format('Y-m-d') : '—',
            'ref'   => '',
            'type'  => 'Opening Balance',
            'in'    => '',
            'out'   => '',
            'qty'   => 0,
            'order' => 0,
        ]);

        /* ---------------------------------------------------
        | 2️⃣ Stock Ledger Rows (date range)
        --------------------------------------------------- */
        $ledgers = DB::table('stock_ledgers')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->when($warehouseId, fn($q) =>
                $q->where('warehouse_id', $warehouseId)
            )
            ->when($from, fn($q) =>
                $q->whereBetween('txn_date', [$from, $to ?? now()])
            )
            ->orderBy('txn_date')
            ->orderBy('id')
            ->get();

        $product   = DB::table('products')->where('id', $productId)->value('name');
        $warehouse = DB::table('warehouses')->where('id', $warehouseId)->value('name');

        foreach ($ledgers as $l) {

            $rows->push([
                'date'      => Carbon::parse($l->txn_date)->format('Y-m-d'),
                'product'   => $product ?? '',
                'warehouse' => $warehouse ?? '',
                'type'      => ucfirst(strtolower($l->ref_type)),
                'ref_type'  => $l->ref_type,
                'ref_id'    => $l->ref_id,
                'in'        => $l->direction === 'IN' ? (float) $l->quantity : 0,
                'out'       => $l->direction === 'OUT' ? (float) $l->quantity : 0,
                'order'     => 1,
            ]);

        }

        /* ---------------------------------------------------
        | 3️⃣ Running Balance
        --------------------------------------------------- */
        $balance = $openingQty;
        $data    = [];
        $sl      = 1;

        foreach ($rows as $r) {

            if ($r['order'] === 1) {
                $balance += ($r['in'] - $r['out']);
            }

            $refHtml = '-';

            if (! empty($r['ref_type']) && ! empty($r['ref_id'])) {
                $refHtml = '
                <a href="#"
                class="AjaxViewModal text-primary-400 fw-semibold"
                data-size="lg"
                data-ajax-modal="' . route('inventory.reports.stock-ledger.reference.view', [
                    'type' => $r['ref_type'],
                    'id'   => $r['ref_id'],
                ]) . '">
                    ' . e($r['ref_type']) . '-' . e($r['ref_id']) . '
                </a>';
            }

            $data[] = [
                $sl++,
                $r['date'],
                $product ?? '',   // For opening row, product name is not needed
                $warehouse ?? '', // For opening row, warehouse name is not needed
                $refHtml,
                $r['type'],
                $r['in'] !== '' ? number_format($r['in'], 2) : '',
                $r['out'] !== '' ? number_format($r['out'], 2) : '',
                number_format($balance, 2),
            ];
        }

        return response()->json([
            'draw'                 => (int) $request->draw,
            'iTotalRecords'        => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData'               => $data,
        ]);
    }

    public function referenceView(string $type, int $id)
    {
        return match (strtolower($type)) {

            'sale'             => view('backend.modules.pos.saleDetailsModal', [
                'sale' => Sale::with('items.product')->findOrFail($id),
            ]),

            'sale_return'      => view('backend.modules.sale_returns.show_modal', [
                'saleReturn' => SaleReturn::with('items.product')->findOrFail($id),
            ]),

            'purchase_receipt' => (function () use ($id) {

                // $id = purchase_receipts.id (46)
                $receipt = PurchaseReceipt::with([
                    'order.items.product',
                    'order.payments',
                    'order.supplier',
                    'order.warehouse',
                    'order.branch',
                ])->findOrFail($id);

                $order = $receipt->order;

                $paid        = $order->payments->sum('amount');
                $outstanding = round($order->total_amount - $paid, 2);

                return view(
                    'backend.modules.purchase.show_modal',
                    compact('order', 'paid', 'outstanding')
                );

            })(),

            'purchase_return'  => (function () use ($id) {

                $order = PurchaseReturn::with([
                    'supplier',
                    'items.product',
                    'purchaseReturnPayment',
                ])->findOrFail($id);

                return view(
                    'backend.modules.purchase_returns.show_modal',
                    compact('order')
                );

            })(),

            'adjustment'       => (function () use ($id) {

                $adjustment = StockAdjustment::with([
                    'warehouse',
                    'branch',
                    'creator',
                    'items.product',
                    'ledgerEntries.product',
                ])->findOrFail($id);

                return view(
                    'backend.modules.inventory.stockAdjustment.show_modal',
                    compact('adjustment')
                );

            })(),
            'transfer'         => (function () use ($id) {

                $transfer = StockTransfer::with([
                    'fromWarehouse.branch',
                    'toWarehouse.branch',
                    'fromBranch',
                    'toBranch',
                    'creator',
                    'items.product',
                ])->findOrFail($id);

                $ledgers = $transfer->ledgerEntries()
                    ->with(['product', 'branch'])
                    ->get();

                return view(
                    'backend.modules.inventory.stockTransfer.show_modal',
                    compact('transfer', 'ledgers')
                );

            })(),

            default            => abort(404, 'Reference not supported'),
        };
    }

    public function summary(Request $request)
    {
        $productId   = $request->product_id;
        $warehouseId = $request->warehouse_id;

        if (! $productId) {
            return response()->json([
                'opening' => 0,
                'in'      => 0,
                'out'     => 0,
                'closing' => 0,
            ]);
        }

        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        $from = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : null;

        $to = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : null;

        /* ---------------- Opening ---------------- */
        $opening = DB::table('stock_ledgers')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->when($warehouseId, fn($q) =>
                $q->where('warehouse_id', $warehouseId)
            )
            ->when($from, fn($q) =>
                $q->where('txn_date', '<', $from)
            )
            ->selectRaw("
            SUM(
                CASE
                    WHEN direction = 'IN'  THEN quantity
                    WHEN direction = 'OUT' THEN -quantity
                    ELSE 0
                END
            ) as qty
        ")
            ->value('qty') ?? 0;

        /* ---------------- IN ---------------- */
        $stockIn = DB::table('stock_ledgers')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('direction', 'IN')
            ->when($warehouseId, fn($q) =>
                $q->where('warehouse_id', $warehouseId)
            )
            ->when($from && $to, fn($q) =>
                $q->whereBetween('txn_date', [$from, $to])
            )
            ->sum('quantity');

        /* ---------------- OUT ---------------- */
        $stockOut = DB::table('stock_ledgers')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('direction', 'OUT')
            ->when($warehouseId, fn($q) =>
                $q->where('warehouse_id', $warehouseId)
            )
            ->when($from && $to, fn($q) =>
                $q->whereBetween('txn_date', [$from, $to])
            )
            ->sum('quantity');

        $closing = round($opening + $stockIn - $stockOut, 2);

        return response()->json([
            'opening' => round($opening, 2),
            'in'      => round($stockIn, 2),
            'out'     => round($stockOut, 2),
            'closing' => $closing,
        ]);
    }

}
