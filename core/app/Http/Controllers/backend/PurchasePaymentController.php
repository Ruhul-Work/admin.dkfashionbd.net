<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\BranchAccount;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\PurchaseOrder;
use App\Models\backend\PurchasePayment;
use App\Models\backend\SupplierLedger;
use App\Models\backend\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchasePaymentController extends Controller
{
    public function storeForOrder(Request $request, PurchaseOrder $order)
    {
        // \Log::info('PAYMENT REQ ' . json_encode($request->all()));
        // \Log::info('RAW CONTENT: ' . $request->getContent());
        // $payload = json_decode($request->input('payload'), true);
        // if (! $payload) {
        //     return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        // }

        // $validator = Validator::make($payload, [
        //     'amount'    => 'required|numeric|min:0.01',
        //     'method'    => 'nullable|string|max:60',
        //     'reference' => 'nullable|string|max:150',
        //     'notes'     => 'nullable|string|max:500',
        //     'payment_date' => 'nullable|date',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        // }

        // DB::beginTransaction();
        // try {
        //     // optional: lock the order row for update to avoid race
        //     $order = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();

        //     $payment = PurchasePayment::create([
        //         'supplier_id'       => $order->supplier_id,
        //         'purchase_order_id' => $order->id,
        //         'payment_date'      => now(),
        //         'amount'            => round((float) $payload['amount'], 2),
        //         'method'            => $payload['method'] ?? null,
        //         'reference'         => $payload['reference'] ?? null,
        //         'notes'             => $payload['notes'] ?? null,
        //         'payment_date'      => $payload['payment_date'] ?? now(),
        //         'created_by'        => auth()->id(),
        //     ]);

        // 1) Try payload JSON first (old behavior)
        $payloadJson = $request->input('payload');
        $payload     = null;

        if ($payloadJson) {
            $payload = json_decode($payloadJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning('Invalid JSON payload: ' . json_last_error_msg());
                return response()->json(['success' => false, 'message' => 'Invalid payload JSON'], 422);
            }
        } else {
            // 2) Normalize direct form fields into the same $payload shape
            $payload = [
                'order_id'     => $request->input('order_id') ?? $request->route('order') ?? null,
                'amount'       => $request->input('amount') ?? $request->input('paid_amount') ?? null,
                'method'       => $request->input('method') ?? $request->input('payment_type') ?? null,
                'reference'    => $request->input('reference') ?? null,
                'notes'        => $request->input('notes') ?? $request->input('payment_note') ?? null,
                'payment_date' => $request->input('payment_date') ?? now()->toDateTimeString(),
            ];
        }



        // quick log to verify
        // \Log::info('Normalized payment payload', $payload);

        // now validate $payload (example)
        $validator = \Validator::make($payload, [
            'order_id'     => 'required|exists:purchase_orders,id',
            'amount'       => 'required|numeric|min:0.01',
            'method'       => 'nullable|string|max:60',
            'reference'    => 'nullable|string|max:150',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }


        $cashAccountId = BranchAccount::where('branch_id', $order->branch_id)
            ->where('is_active', 1)
            ->value('account_id');

        if (! $cashAccountId) {
            return response()->json(['success' => false, 'message' => 'Cash Account not found'], 422);
        }

        // proceed: use $payload values to create PurchasePayment etc.
        // e.g.
        DB::beginTransaction();
        try {
            $order = PurchaseOrder::where('id', $order->id)->lockForUpdate()->first();
            // recompute outstanding
            $paid        = $order->payments()->sum('amount'); // previous paid
            $outstanding = round($order->total_amount - $paid, 2);
            if ($payload['amount'] > $outstanding) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Payment exceeds outstanding amount'], 422);
            }

            // your existing checks: outstanding, lockForUpdate etc.
            // create payment record:
            $payment = PurchasePayment::create([
                'supplier_id'       => $order->supplier_id,
                'branch_id'         =>  $order->branch_id ?? null,
                'purchase_order_id' => $order->id,
                'payment_date'      => $payload['payment_date'],
                'amount'            => round((float) $payload['amount'], 2),
                'method'            => $payload['method'] ?? null,
                'reference'         => $payload['reference'] ?? null,
                'notes'             => $payload['notes'] ?? null,
                'created_by'        => auth()->id(),
            ]);


            $journal = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('PURCHASE'),
                'voucher_type_id' => VoucherType::idByCode('PURCHASE'),
                'branch_id'       => $order->branch_id,
                'fiscal_year_id'  => currentFiscalYear()->id,
                'source_id'       => $order->id,
                'entry_date'      => now()->toDateString(),
                'narration'       => 'Purchase#' . $order->po_number,
                'created_by'      => auth()->id(),
            ]);

            // JournalEntryLine::create([
            //     'journal_entry_id' => $journal->id,
            //     'account_id'       => $cashAccountId,
            //     'branch_id'        => $order->branch_id,
            //     'debit'            => $payload['amount'],
            //     'credit'           => 0,
            // ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $cashAccountId,
                'branch_id'        => $order->branch_id,
                'debit'            => 0,
                'credit'           => $payload['amount'],
            ]);


            // ledger ...
            // get last ledger for supplier

            $last = SupplierLedger::where('supplier_id', $order->supplier_id)
                ->where('branch_id', $payload['branch_id'] ?? $order->branch_id)
                ->orderBy('id', 'desc')
                ->first();

            $prev_balance = $last ? (float) $last->balance_after : 0.00;

            // assuming: debit = new purchase (increase liability), credit = payment (decrease)
            $new_balance = $prev_balance - $payment->amount;

            SupplierLedger::create([
                'supplier_id'    => $order->supplier_id,
                'branch_id'        => $order->branch_id,
                'reference_type' => 'purchase_payment',
                'reference_id'   => $payment->id,
                'txn_date'       => now()->toDateString(),
                'description'    => 'Payment for PO ' . $order->po_number,
                'debit'          => 0.00,
                'credit'         => $payment->amount,
                'balance_after'  => $new_balance,
            ]);

            // recompute and save
            $paid                      = $order->payments()->sum('amount');
            $order->paid_amount        = round($paid, 2);
            $order->outstanding_amount = max(0, round($order->total_amount - $order->paid_amount, 2));
            $order->payment_status     = $order->paid_amount <= 0 ? 'unpaid' : ($order->outstanding_amount <= 0 ? 'paid' : 'partially_paid');
            $order->save();

            DB::commit();
            return response()->json([
                'success'                => true,
                'message'                => 'Payment recorded',
                'payment'                => $payment,
                'payment_date_formatted' => $payment->payment_date->format('d-m-Y'),
                'new_summary'            => [
                    'paid_amount'    => (float) $order->paid_amount,
                    'outstanding'    => (float) $order->outstanding_amount,
                    'payment_status' => $order->payment_status,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Add payment failed: ' . $e->getMessage());
            return  response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
