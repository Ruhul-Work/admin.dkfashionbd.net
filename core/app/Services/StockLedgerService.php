<?php

namespace App\Services;

use App\Models\backend\StockCurrent;
use App\Models\backend\StockLedger;
use Exception;
use Illuminate\Support\Facades\DB;

class StockLedgerService
{
    /**
     * Deduct stock for SALE (warehouse wise)
     */
    // public static function deductForSale(array $params): void
    // {
    //     /*
    //     $params = [
    //         'sale_id' => 10,
    //         'warehouse_id' => 1,
    //         'branch_id' => 1,
    //         'user_id' => 1,
    //         'items' => [
    //             [
    //                 'product_id' => 5,
    //                 'quantity' => 2,
    //                 'unit_price' => 500
    //             ]
    //         ]
    //     ];
    //     */

    //     foreach ($params['items'] as $item) {

    //         $qty = abs($item['quantity']);

    //         // -----------------------------
    //         // 1️⃣ Lock current stock row
    //         // -----------------------------
    //         // $stock = StockCurrent::where('warehouse_id', $params['warehouse_id'])
    //         //     ->where('branch_id', $params['branch_id'])
    //         //     ->where('product_id', $item['product_id'])
    //         //     ->lockForUpdate()
    //         //     ->first();

    //         \Log::info('STOCK LOOKUP', [
    //             'branch_id'    => $params['branch_id'],
    //             'warehouse_id' => $params['warehouse_id'],
    //             'product_id'   => $item['product_id'],
    //             'qty_needed'   => $qty,
    //             'stock_row'    => StockCurrent::withoutGlobalScope('branch')
    //                 ->where('warehouse_id', $params['warehouse_id'])
    //                 ->where('branch_id', $params['branch_id'])
    //                 ->where('product_id', $item['product_id'])
    //                 ->first(),
    //         ]);

    //         $stock = StockCurrent::withoutGlobalScope('branch')
    //             ->where('warehouse_id', $params['warehouse_id'])
    //             ->where('branch_id', $params['branch_id'])
    //             ->where('product_id', $item['product_id'])
    //             ->lockForUpdate()
    //             ->first();

    //         $product = DB::table('products')->where('id', $item['product_id'])->first();

    //         if (! $stock || $stock->quantity < $qty) {
    //             throw new Exception(
    //                 'Insufficient stock for "' . ($product->name ?? 'Product') . '"'
    //             );
    //         }

    //         // -----------------------------
    //         // 2️⃣ Insert stock ledger (OUT)
    //         // -----------------------------
    //         StockLedger::create([
    //             'txn_date'     => now(),
    //             'product_id'   => $item['product_id'],
    //             'warehouse_id' => $params['warehouse_id'],
    //             'branch_id'    => $params['branch_id'],

    //             'ref_type'     => 'sale',
    //             'ref_id'       => $params['sale_id'],
    //             'direction'    => 'out',

    //             'quantity'     => $qty, // always positive
    //             'unit_cost'    => $item['unit_price'],

    //             'note'         => 'POS Sale',
    //             'created_by'   => $params['user_id'],
    //         ]);

    //         // -----------------------------
    //         // 3️⃣ Update stock_currents
    //         // -----------------------------
    //         $stock->update([
    //             'quantity' => $stock->quantity - $qty,
    //             'version'  => $stock->version + 1,
    //         ]);
    //     }
    // }

    /* =========================================================
     | PUBLIC APIs (Controllers call ONLY these)
     ========================================================= */

    /**
     * SALE → stock OUT
     */
    public static function deductForSale(array $params): void
    {
        foreach ($params['items'] as $item) {
            self::adjustStockCore([
                'product_id'   => $item['product_id'],
                'warehouse_id' => $params['warehouse_id'],
                'branch_id'    => $params['branch_id'],
                'qty'          => $item['quantity'],
                'direction'    => 'out',
                'ref_type'     => $params['ref_type'] ?? 'SALE',
                'ref_id'       => $params['sale_id'],
                'unit_cost'    => $item['unit_price'] ?? null,
                'note'         => 'POS Sale',
                'user_id'      => $params['user_id'],
            ]);
        }
    }

    /**
     * PURCHASE → stock IN
     */
    public static function increaseForPurchase(array $params): void
    {
        foreach ($params['items'] as $item) {
            self::adjustStockCore([
                'product_id'   => $item['product_id'],
                'warehouse_id' => $params['warehouse_id'],
                'branch_id'    => $params['branch_id'],
                'qty'          => $item['quantity'],
                'direction'    => 'in',
                'ref_type'     => 'PURCHASE_RECEIPT',
                'ref_id'       => $params['receipt_id'],
                'unit_cost'    => $item['unit_cost'] ?? null,
                'note'         => 'Received via Purchase receipt #' . $params['receipt_id'],
                'user_id'      => $params['user_id'],
            ]);
        }
    }

    /**
     * SALE RETURN → stock IN
     */
    public static function increaseForSaleReturn(array $params): void
    {
        foreach ($params['items'] as $item) {
            self::adjustStockCore([
                'product_id'   => $item['product_id'],
                'warehouse_id' => $params['warehouse_id'],
                'branch_id'    => $params['branch_id'],
                'qty'          => $item['quantity'],
                'direction'    => 'in',
                'ref_type'     => $params['ref_type'] ?? 'SALE_RETURN', //
                'ref_id'       => $params['sale_id'] ?? $params['sale_return_id'],
                'unit_cost'    => $item['unit_price'] ?? null,
                'note'         => 'Stock returned from Sale',
                'user_id'      => $params['user_id'],
            ]);
        }
    }

    /**
     * PURCHASE RETURN → stock OUT (future ready)
     */
    public static function deductForPurchaseReturn(array $params): void
    {
        foreach ($params['items'] as $item) {
            self::adjustStockCore([
                'product_id'   => $item['product_id'],
                'warehouse_id' => $params['warehouse_id'],
                'branch_id'    => $params['branch_id'],
                'qty'          => $item['quantity'],
                'direction'    => 'out',
                'ref_type'     => 'PURCHASE_RETURN',
                'ref_id'       => $params['purchase_return_id'],
                'unit_cost'    => $item['unit_cost'] ?? null,
                'note'         => 'Returned to Supplier',
                'user_id'      => $params['user_id'],
            ]);
        }
    }

    /* =========================================================
     | CORE ENGINE (DO NOT call from controller)
     ========================================================= */

    protected static function adjustStockCore(array $data): void
    {
        DB::transaction(function () use ($data) {

            $qty = abs((float) $data['qty']);
            if ($qty <= 0) {
                return;
            }

            // 🔒 Lock stock row
            $stock = StockCurrent::withoutGlobalScope('branch')
                ->where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->where('branch_id', $data['branch_id'])
                ->lockForUpdate()
                ->first();

            // ❌ Insufficient stock protection
            if ($data['direction'] === 'out') {
                if (! $stock || $stock->quantity < $qty) {
                    throw new Exception('Insufficient stock for product ID ' . $data['product_id']);
                }
            }

            // 🧮 New quantity
            $newQty = $data['direction'] === 'in'
                ? ($stock->quantity ?? 0) + $qty
                : ($stock->quantity - $qty);

            // 🆕 Create or Update stock_current
            if ($stock) {
                $stock->update([
                    'quantity' => $newQty,
                    'version'  => $stock->version + 1,
                ]);
            } else {
                StockCurrent::create([
                    'product_id'   => $data['product_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'branch_id'    => $data['branch_id'],
                    'quantity'     => $newQty,
                    'version'      => 1,
                ]);
            }

            // 🧾 Ledger entry
            StockLedger::create([
                'txn_date'     => now()->toDateString(),
                'product_id'   => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'branch_id'    => $data['branch_id'],
                'ref_type'     => $data['ref_type'],
                'ref_id'       => $data['ref_id'],
                'direction'    => $data['direction'],
                'quantity'     => $qty,
                'unit_cost'    => $data['unit_cost'],
                'note'         => $data['note'],
                'created_by'   => $data['user_id'] ?? Auth::id(),
            ]);
        }, 5); // retry safe
    }
}
