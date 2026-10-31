<?php
namespace App\Services;

use App\Models\backend\BranchAccount;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\PurchaseReturn;
use App\Models\backend\PurchaseReturnItem;
use App\Models\backend\PurchaseReturnPayment;
use App\Models\backend\StockCurrent;
use App\Models\backend\StockLedger;
use App\Models\backend\SupplierLedger;
use App\Models\backend\VoucherType;
use function Symfony\Component\Clock\now;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnService
{
    public static function returnPurchaseOrder($payload)
    {
        $payload = is_array($payload) ? $payload : json_decode($payload, true);
        // dd($payload['items']);

        $refundTotal = 0;
        $returnItems = [];

        $productIds = collect($payload['items'])->pluck('product_id')->unique();

        /*
|--------------------------------------------------------------------------
| Preload Required Data (ONE QUERY EACH)
|--------------------------------------------------------------------------
*/

        // Current stock per product
        $stockMap = StockCurrent::where('warehouse_id', $payload['warehouse_id'])
            ->where('branch_id', $payload['branch_id'])
            ->whereIn('product_id', $productIds)
            ->pluck('quantity', 'product_id');

        // dd($payload['branch_id']);

        /*
|--------------------------------------------------------------------------
| Validation + Build Return Items
|--------------------------------------------------------------------------
*/

        foreach ($payload['items'] as $returnItem) {

            $productId = $returnItem['product_id'];
            $returnQty = (int) $returnItem['quantity'];

            if ($returnQty <= 0) {
                return response()->json([
                    'success'    => false,
                    'message'    => 'Invalid return quantity',
                    'product_id' => $productId,
                ], 422);
            }

            $purchasedQty  = (int) ($purchasedQtyMap[$productId] ?? 0);
            $returnableQty = $stockMap[$productId];

            // if ($returnQty > $returnableQty) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Return quantity exceeds remaining returnable quantity',
            //         'product_id' => $productId,
            //         'returnable_qty' => $returnableQty
            //     ], 422);
            // }

            $unitCost  = (float) ($returnItem['unit_cost'] ?? 0);
            $lineTotal = $unitCost * $returnQty;

            $refundTotal += $lineTotal;

            $returnItems[] = [
                'product_id' => $productId,
                'return_qty' => $returnQty,
                'price'      => $unitCost,
                'total'      => $lineTotal,
            ];
        }

        /*
|--------------------------------------------------------------------------
| Final Safety Check
|--------------------------------------------------------------------------
*/

        if ($refundTotal <= 0 || empty($returnItems)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid return items found',
            ], 422);
        }

        // $cashAccountId = BranchAccount::where('branch_id', $payload['branch_id'])
        //     ->where('is_active', 1)
        //     ->value('account_id');

        // if (! $cashAccountId) {
        //     return response()->json(['success' => false, 'message' => 'Cash Account not found'], 422);
        // }

        // 🔒 Safety: must belong to current branch
        $branchId  = current_branch_id();
        /* ---------------------------------------------------
        | 1️⃣ Get default cash/bank account
        --------------------------------------------------- */
        $accountId = BranchAccount::where('branch_id', $branchId)
            ->where('is_default', 1)
            ->value('account_id');

        if (! $accountId) {
            throw new \Exception('Default branch account not configured.');
        }

        $discount_value = 0;

        if ($payload['discount']['type'] == 'flat') {
            $discount_value = $payload['discount']['value'];
        } elseif ($payload['discount']['type'] == 'percentage') {
            $discount_value = ($refundTotal * $payload['discount']['value'] / 100);
        }

        $return_number = 'PR-' . time();
        DB::beginTransaction();
        try {
            $purchase_return_id = PurchaseReturn::create([
                'supplier_id'     => $payload['supplier_id'],
                'warehouse_id'    => $payload['warehouse_id'],
                'branch_id'       => $payload['branch_id'],
                'return_number'   => $return_number,
                'return_date'     => $payload['order_date'],
                'reference'       => $payload['reference'] ?? null,
                'subtotal'        => $refundTotal,
                'discount'        => $discount_value,
                'shipping_charge' => $payload['shipping_amount'] ?? 0,
                'total_amount'    => $refundTotal - $discount_value + ($payload['shipping_amount'] ?? 0),
                'paid_amount'     => $payload['payment']['amount'] ?? 0,
                'due_amount'      => $refundTotal - $discount_value + ($payload['shipping_amount'] ?? 0) - ($payload['payment']['amount'] ?? 0),
                'created_by'      => Auth::user()->id,
                'note'            => $payload['note'] ?? null,
            ]);

            foreach ($returnItems as $returnItem) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchase_return_id->id,
                    'product_id'         => $returnItem['product_id'],
                    'quantity'           => $returnItem['return_qty'],
                    'unit_cost'          => $returnItem['price'],
                    'line_total'         => $returnItem['total'],
                ]);

                StockLedger::create([
                    'product_id'   => $returnItem['product_id'],
                    'warehouse_id' => $payload['warehouse_id'],
                    'branch_id'    => $payload['branch_id'],
                    'quantity'     => $returnItem['return_qty'],
                    'direction'    => 'OUT',
                    'ref_type'     => 'PURCHASE_RETURN',
                    'ref_id'       => $purchase_return_id->id,
                    'unit_cost'    => $returnItem['price'],
                    'note'         => 'Purchase Return #' . $return_number,
                    'txn_date'     => now(),
                    'created_by'   => Auth::user()->id,
                    'notes'        => 'Purchase Return ',
                ]);

                $query = StockCurrent::where('product_id', $returnItem['product_id'])
                    ->where('warehouse_id', $payload['warehouse_id'])
                    ->where('branch_id', $payload['branch_id']);

                $query->decrement('quantity', $returnItem['return_qty']);
                $query->increment('version', 1);
            }
            if (($payload['payment']['amount'] ?? 0) > 0) {
                $paymentId = PurchaseReturnPayment::create([
                    'purchase_return_id' => $purchase_return_id->id,
                    'supplier_id'        => $payload['supplier_id'],
                    'branch_id'          => $payload['branch_id'],
                    'amount'             => $payload['payment']['amount'] ?? 0,
                    'method'             => $payload['payment']['method'] ?? 'cash',
                    'note'               => $payload['payment']['notes'] ?? null,
                    'created_by'         => Auth::user()->id,
                ]);

                // $revenueAccountId = config('accounting.sales_revenue_account_id');

                $journal = JournalEntry::create([
                    'voucher_no'      => generateVoucherNo('REFUND'),
                    'voucher_type_id' => VoucherType::idByCode('REFUND'),
                    'branch_id'       => $payload['branch_id'],
                    'fiscal_year_id'  => currentFiscalYear()->id,
                    'source_id'       => $paymentId->id,
                    'entry_date'      => now(),
                    'narration'       => 'Purchase Return ' . $return_number,
                    'created_by'      => auth()->id(),
                ]);

                // JournalEntryLine::create([
                //     'journal_entry_id' => $journal->id,
                //     'account_id'       => $cashAccountId,
                //     'branch_id'        => $payload['branch_id'],
                //     'debit'            => 0,
                //     'credit'           => $payload['payment']['amount']??0,
                // ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id'       => $accountId ?? null,
                    'branch_id'        => $payload['branch_id'],
                    'debit'            => $payload['payment']['amount'] ?? 0,
                    'credit'           => 0,
                ]);

                SupplierLedger::create([
                    'supplier_id'    => $payload['supplier_id'],
                    'branch_id'      => $payload['branch_id'],
                    'reference_type' => 'purchase_return_payment',
                    'reference_id'   => $paymentId->id,
                    'txn_date'       => now(),
                    'description'    => 'Refund for PO ' . $return_number,
                    'debit'          => $payload['payment']['amount'] ?? 0,
                    'credit'         => 0.00,
                    'balance_after'  => 0.00, // This should be calculated based on previous balance
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Purchase Return processed successfully'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Add payment failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function storePayment(PurchaseReturn $purchaseReturn, $payload)
    {
        $cashAccountId = BranchAccount::where('branch_id', $purchaseReturn->branch_id)
            ->where('is_active', 1)
            ->value('account_id');

        if (! $cashAccountId) {
            return response()->json(['success' => false, 'message' => 'Cash Account not found'], 422);
        }

        DB::beginTransaction();
        try {
            $purchaseReturn->paid_amount += $payload['amount'];
            $purchaseReturn->due_amount   = $purchaseReturn->total_amount - $purchaseReturn->paid_amount;
            $purchaseReturn->save();

            $payment = PurchaseReturnPayment::create([
                'purchase_return_id' => $purchaseReturn->id,
                'supplier_id'        => $purchaseReturn->supplier_id,
                'branch_id'          => $purchaseReturn->branch_id,
                'amount'             => $payload['amount'],
                'method'             => $payload['method'],
                'note'               => $payload['notes'] ?? null,
                'created_by'         => Auth::user()->id,
            ]);
            $revenueAccountId = config('accounting.sales_revenue_account_id');
            $journal          = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('REFUND'),
                'voucher_type_id' => VoucherType::idByCode('REFUND'),
                'branch_id'       => $purchaseReturn->branch_id,
                'fiscal_year_id'  => currentFiscalYear()->id,
                'source_id'       => $payment->id,
                'entry_date'      => $payload['payment_date'],
                'narration'       => 'Payment for Purchase Return#' . $purchaseReturn->return_number,
                'created_by'      => auth()->id(),
            ]);
            // JournalEntryLine::create([
            //     'journal_entry_id' => $journal->id,
            //     'account_id'       => $cashAccountId,
            //     'branch_id'        => $purchaseReturn->branch_id,
            //     'debit'            => 0,
            //     'credit'           => $payload['amount'],
            // ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $cashAccountId,
                'branch_id'        => $purchaseReturn->branch_id,
                'debit'            => $payload['amount'],
                'credit'           => 0,
            ]);

            SupplierLedger::create([
                'supplier_id'    => $purchaseReturn->supplier_id,
                'branch_id'      => $purchaseReturn->branch_id,
                'reference_type' => 'purchase_return_payment',
                'reference_id'   => $payment->id,
                'txn_date'       => $payload['payment_date'],
                'description'    => 'Payment for Purchase Return ' . $purchaseReturn->return_number,
                'debit'          => $payment->amount,
                'credit'         => 0.00,
                'balance_after'  => 0.00, // This should be calculated based on previous balance
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'payment' => $payment,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Add payment failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
