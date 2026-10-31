<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\PaymentType;
use App\Models\backend\Sale;
use App\Models\backend\SalePayment;
use App\Models\backend\BranchAccount;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\VoucherType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class PosSalePaymentController extends Controller
{
    public function create(Sale $sale)
    {
        abort_if($sale->due_amount <= 0, 422, 'Sale has no due.');

        return view('backend.modules.pos.payment_modal', [
            'sale'         => $sale,
            'paymentTypes' => PaymentType::where('is_active', 1)->get(),
        ]);
    }

    public function store(Request $request, Sale $sale)
    {
        abort_if($sale->due_amount <= 0, 422, 'No due to receive.');

        $data = $request->validate([
            'payments'          => 'required|array|min:1',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0.01',
        ]);

        $payments = collect($data['payments']);

        $received = round(
            $payments->sum(fn($p) => (float) $p['amount']),
            2
        );

        if ($received > $sale->due_amount) {
            throw ValidationException::withMessages([
                'payments' => 'Payment exceeds due amount.',
            ]);
        }

        return DB::transaction(function () use ($sale, $payments, $received) {

            // update sale
            $sale->update([
                'paid_amount'    => $sale->paid_amount + $received,
                'due_amount'     => $sale->due_amount - $received,
                'payment_status' => ($sale->due_amount - $received) <= 0 ? 'paid' : 'partial',
            ]);

            $cashAccountId = BranchAccount::where('branch_id', $sale->branch_id)
                ->where('is_active', 1)
                ->value('account_id');

            foreach ($payments as $pay) {

                $type = PaymentType::where('slug', $pay['method'])->firstOrFail();

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

            // journal
            $journal = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('PAY'),
                'voucher_type_id' => VoucherType::idByCode('PAYMENT'),
                'branch_id'       => $sale->branch_id,
                'fiscal_year_id'  => currentFiscalYear()->id,
                'source_id'       => $sale->id,
                'entry_date'      => now()->toDateString(),
                'narration'       => 'Payment received for ' . $sale->invoice_no,
                'created_by'      => auth()->id(),
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $cashAccountId,
                'branch_id'        => $sale->branch_id,
                'debit'            => $received,
                'credit'           => 0,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => config('accounting.sales_revenue_account_id'),
                'branch_id'        => $sale->branch_id,
                'debit'            => 0,
                'credit'           => $received,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment received successfully.',
            ]);
        });
    }
}
