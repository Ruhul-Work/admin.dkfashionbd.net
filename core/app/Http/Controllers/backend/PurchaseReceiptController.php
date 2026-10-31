<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\PurchaseOrder;
use App\Models\backend\PurchaseReceipt;
use App\Models\backend\PurchaseReceiptItem;
use App\Models\backend\StockCurrent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\StockLedgerService;

class PurchaseReceiptController extends Controller
{
     public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date'      => 'nullable|date',
            'note'              => 'nullable|string|max:500',
        ]);

        $order = PurchaseOrder::with('items.product')->findOrFail($data['purchase_order_id']);

        if ($order->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Order not in draft status or already received',
            ], 422);
        }

        $toReceive = [];
        foreach ($order->items as $it) {
            $remaining = (float) $it->quantity - (float) ($it->received_quantity ?? 0);
            if ($remaining > 0) {
                $toReceive[] = [
                    'order_item' => $it,
                    'quantity'   => $remaining,
                ];
            }
        }

        if (count($toReceive) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nothing to receive',
            ], 422);
        }

        DB::beginTransaction();

        try {

            /* -----------------------------
            | 1️⃣ Create receipt
            ----------------------------- */
            $receipt = PurchaseReceipt::create([
                'supplier_id'       => $order->supplier_id,
                'branch_id'         => $order->branch_id,
                'warehouse_id'      => $order->warehouse_id,
                'purchase_order_id' => $order->id,
                'receipt_date'      => $data['receipt_date'] ?? now(),
                'invoice_no'        => $order->po_number,
                'note'              => $data['note'] ?? null,
                'created_by'        => auth()->id(),
            ]);

            $stockItems = [];

            /* -----------------------------
            | 2️⃣ Receipt items + order update
            ----------------------------- */
            foreach ($toReceive as $r) {

                $it  = $r['order_item'];
                $qty = $r['quantity'];

                PurchaseReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $it->product_id,
                    'quantity'   => $qty,
                    'unit_cost'  => $it->unit_cost,
                    'uom_id'     => $it->uom_id ?? null,
                ]);

                $it->received_quantity = ((float) $it->received_quantity) + $qty;
                $it->save();

                // prepare for stock service
                $stockItems[] = [
                    'product_id' => $it->product_id,
                    'quantity'   => $qty,
                    'unit_cost'  => $it->unit_cost,
                ];
            }

            /* -----------------------------
            | 3️⃣ STOCK IN (🔥 SERVICE CALL)
            ----------------------------- */
            StockLedgerService::increaseForPurchase([
                'receipt_id'   => $receipt->id,
                'branch_id'    => $order->branch_id,
                'warehouse_id' => $order->warehouse_id,
                'user_id'      => auth()->id(),
                'items'        => $stockItems,
            ]);

            /* -----------------------------
            | 4️⃣ Update order status
            ----------------------------- */
            $order->status = 'received';
            $order->save();

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Received and stock updated',
                'receipt_id' => $receipt->id,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Receive failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ], 500);
        }
    }


    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'purchase_order_id' => 'required|exists:purchase_orders,id',
    //         'receipt_date'      => 'nullable|date',
    //         'note'              => 'nullable|string|max:500',
    //     ]);

    //     // load order + items
    //     $order = PurchaseOrder::with('items.product')->findOrFail($data['purchase_order_id']);

    //     if ($order->status !== 'draft') {
    //         return response()->json(['success' => false, 'message' => 'Order not in draft status or already received'], 422);
    //     }

    //     // compute remaining items to receive (full receive behavior)
    //     $toReceive = [];
    //     foreach ($order->items as $it) {
    //         $remaining = (float) $it->quantity - (float) ($it->received_quantity ?? 0);
    //         if ($remaining > 0) {
    //             $toReceive[] = ['order_item' => $it, 'quantity' => $remaining];
    //         }
    //     }

    //     if (count($toReceive) === 0) {
    //         return response()->json(['success' => false, 'message' => 'Nothing to receive'], 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // create receipt record
    //         $receipt = PurchaseReceipt::create([
    //             'supplier_id'       => $order->supplier_id,
    //             'branch_id'         => $order->branch_id,
    //             'warehouse_id'      => $order->warehouse_id,
    //             'purchase_order_id' => $order->id,
    //             'receipt_date'      => $data['receipt_date'] ?? now(),
    //             'invoice_no'        => $order->po_number, // you said no invoice image required
    //             'note'              => $data['note'] ?? null,
    //             'created_by'        => auth()->id(),
    //         ]);

    //         // create receipt items and update order items + stock
    //         foreach ($toReceive as $r) {
    //             $it  = $r['order_item'];
    //             $qty = $r['quantity'];

    //             PurchaseReceiptItem::create([
    //                 'receipt_id' => $receipt->id,
    //                 'product_id' => $it->product_id,
    //                 'quantity'   => $qty,
    //                 'unit_cost'  => $it->unit_cost,
    //                 'uom_id'     => $it->uom_id ?? null,
    //             ]);

    //             // update order item received_quantity
    //             $it->received_quantity = ((float) $it->received_quantity) + $qty;
    //             $it->save();

    //             // update stock (implement increaseStock according to your schema)
    //             $this->increaseStock($it->product_id, $order->warehouse_id, $qty, $receipt->id, $it->unit_cost);
    //         }

    //         // mark order as received
    //         $order->status = 'received';
    //         $order->save();

    //         DB::commit();

    //         return response()->json(['success' => true, 'message' => 'Received and stock updated', 'receipt_id' => $receipt->id], 201);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Receive failed: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'message' => 'Server error'], 500);
    //     }
    // }

   
    /**
     * Increase stock for a product in a warehouse and write ledger.
     *
     * @param int $productId
     * @param int|null $warehouseId
     * @param float $qty
     * @param int|null $receiptId   // reference id (purchase_receipt id)
     * @param float|null $unitCost
     * @param int|null $branchId
     * @return StockCurrent|null
     */

//     protected function increaseStock(int $productId, $warehouseId = null, $qty = 0, $receiptId = null, $unitCost = null, $branchId = null)
// {
//     $qty = (float) $qty;
//     if ($qty <= 0) return null;

//     // resolve branch/warehouse from receipt if not provided
//     if (empty($branchId) && ! empty($receiptId)) {
//         $rc = PurchaseReceipt::find($receiptId);
//         if ($rc) {
//             $branchId = $branchId ?? $rc->branch_id;
//             $warehouseId = $warehouseId ?? $rc->warehouse_id;
//         }
//     }

//     // Normalize nulls explicitly (make DB comparisons consistent)
//     $warehouseId = $warehouseId === null ? null : (int)$warehouseId;
//     $branchId = $branchId === null ? null : (int)$branchId;

//     return DB::transaction(function () use ($productId, $warehouseId, $branchId, $qty, $receiptId, $unitCost) {

//         // 1) try to lock existing row
//         $row = StockCurrent::where('product_id', $productId)
//             ->where('warehouse_id', $warehouseId)
//             ->where('branch_id', $branchId)
//             ->lockForUpdate()
//             ->first();

//         if ($row) {
//             // update existing
//             $row->quantity = (float)$row->quantity + $qty;
//             $row->version = ((int)$row->version) + 1;
//             $row->save();
//             $sc = $row;
//         } else {
//             // no existing row — try create, but guard against duplicate-race via try/catch
//             try {
//                 $sc = StockCurrent::create([
//                     'product_id' => $productId,
//                     'warehouse_id' => $warehouseId,
//                     'branch_id' => $branchId,
//                     'quantity' => $qty,
//                     'version' => 1,
//                 ]);
//             } catch (\Illuminate\Database\QueryException $ex) {
//                 // If duplicate key happened (another tx inserted between our select and insert),
//                 // fetch that row (no lock needed inside same transaction because DB will handle concurrency)
//                 // then update it.
//                 // Optionally inspect $ex->errorInfo to be safe.
//                 $sc = StockCurrent::where('product_id', $productId)
//                     ->where('warehouse_id', $warehouseId)
//                     ->where('branch_id', $branchId)
//                     ->lockForUpdate()
//                     ->first();

//                 if (! $sc) {
//                     // Very unlikely, rethrow if something odd
//                     throw $ex;
//                 }

//                 $sc->quantity = (float)$sc->quantity + $qty;
//                 $sc->version = ((int)$sc->version) + 1;
//                 $sc->save();
//             }
//         }

//         // 2) ledger entry (inside same tx)
//         StockLedger::create([
//             'txn_date' => now()->toDateString(),
//             'product_id' => $productId,
//             'warehouse_id' => $warehouseId,
//             'branch_id' => $branchId,
//             'ref_type' => 'purchase_receipt',
//             'ref_id' => $receiptId,
//             'direction' => 'in',
//             'quantity' => $qty,
//             'unit_cost' => $unitCost !== null ? round((float)$unitCost, 2) : null,
//             'note' => 'Received via purchase receipt' . ($receiptId ? ' #' . $receiptId : ''),
//             'created_by' => Auth::id(),
//         ]);

//         return $sc;
//     }, 5); // optional retry attempts for deadlocks
// }

    public function receiveAllModal(PurchaseOrder $order)
    {
        // return a view for the "Receive All" modal
        return view('backend.modules.purchase.receipt_modal', compact('order'));
    }
}
