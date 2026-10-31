<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use App\Models\backend\Branch;
use App\Models\backend\FiscalYear;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\OpeningBalance;
use App\Models\backend\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        // UI only for now
        $branches    = Branch::orderBy('name')->get();
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();

        return view(
            'backend.modules.accountOpenBalance.index', compact('branches', 'fiscalYears')
        );
    }

    public function save(Request $request)
    {
        $request->validate([
            'branch_id'      => 'required|exists:branches,id',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'balances'       => 'nullable|array',
            'balances.*'     => 'nullable|numeric',
        ]);

        // ✅ HARD GUARD (helper use)
        $currentFY   = requireFiscalYear();
        $warehouseId = current_warehouse_id();
        $accountId   = $request->input('account_id');

        // 🔒 extra safety: user-selected FY ≠ active FY → block
        if ((int) $request->fiscal_year_id !== (int) $currentFY->id) {
            return redirect()->back()
                ->withErrors('Selected fiscal year is not the active fiscal year.');
        }

        foreach ($request->balances ?? [] as $accountId => $amount) {
            if ($amount !== null && (float) $amount < 0) {
                throw ValidationException::withMessages([
                    "balances.$accountId" => 'Opening balance cannot be negative',
                ]);
            }
        }
        DB::transaction(function () use ($request, $currentFY) {

            $branchId = $request->branch_id;
            $fyId     = $request->fiscal_year_id;
            $balances = $request->balances ?? [];

            /* 1️⃣ Save opening_balances */
            foreach ($balances as $accountId => $amount) {

                if ((float) $amount == 0) {
                    OpeningBalance::where([
                        'branch_id'      => $branchId,
                        'fiscal_year_id' => $fyId,
                        'account_id'     => $accountId,
                    ])->delete();
                } else {
                    OpeningBalance::updateOrCreate(
                        [
                            'branch_id'      => $branchId,
                            'fiscal_year_id' => $fyId,
                            'account_id'     => $accountId,
                        ],
                        ['amount' => $amount]
                    );
                }
            }

            /* 2️⃣ Remove previous OPENING journal */
            $openingVoucherId = VoucherType::idByCode('OPENING');

            $openingEntries = JournalEntry::where([
                'branch_id'       => $branchId,
                'fiscal_year_id'  => $fyId,
                'voucher_type_id' => $openingVoucherId,
            ])->pluck('id');

            JournalEntryLine::whereIn('journal_entry_id', $openingEntries)->delete();
            JournalEntry::whereIn('id', $openingEntries)->delete();

            /* 3️⃣ Filter non-zero balances */
            $nonZeroBalances = array_filter($balances, fn($a) => (float) $a != 0);

            if (empty($nonZeroBalances)) {
                return;
            }

            /* 4️⃣ Create OPENING journal */
            $journal = JournalEntry::create([
                'voucher_no'      => 'OPEN-' . $currentFY->start_date->format('Y') . '-' . $branchId . '-' . current_warehouse_id(),
                'voucher_type_id' => $openingVoucherId,
                'branch_id'       => $branchId,
                'fiscal_year_id'  => $fyId,
                'source_id'       => $branchId,
                'source_ref_id'   => $accountId ?? null,
                'entry_date'      => $currentFY->start_date,
                'narration'       => 'Opening Balance',
                'created_by'      => auth()->id(),
            ]);

            /* 5️⃣ Journal lines */
            foreach ($nonZeroBalances as $accountId => $amount) {

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id'       => $accountId,
                    'branch_id'        => $branchId,
                    'debit'            => abs($amount),
                    'credit'           => 0,
                ]);
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Opening balances saved successfully.');
    }

    public function loadAccounts($branchId, $fiscalYearId)
    {
        // only accounts assigned to branch
        $accounts = Account::where('is_active', 1)
            ->whereHas('branchAccounts', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['openingBalances' => function ($q) use ($branchId, $fiscalYearId) {
                $q->where('branch_id', $branchId)
                    ->where('fiscal_year_id', $fiscalYearId);
            }])
            ->orderBy('name')
            ->get();

        $data = $accounts->map(function ($acc) {
            return [
                'id'     => $acc->id,
                'name'   => $acc->name,
                'amount' => $acc->openingBalances->first()->amount ?? 0,
            ];
        });

        return response()->json($data);
    }

}
