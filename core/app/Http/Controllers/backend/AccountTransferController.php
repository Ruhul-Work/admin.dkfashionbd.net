<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use App\Models\backend\AccountTransfer;
use App\Models\backend\BranchAccount;
use App\Models\backend\JournalEntry;
use App\Models\backend\JournalEntryLine;
use App\Models\backend\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransferController extends Controller
{

    // public function index()
    // {
    //     $branchId = current_branch_id();
    //     abort_if(! $branchId, 422, 'Please select a branch');
    //     return view('backend.modules.account.account_transfers.index');
    // }

    public function index()
    {
        $branchId = current_branch_id();

        if (! $branchId) {
            return redirect()->back()
                ->with('error', 'Please select a branch');
        }

        return view('backend.modules.account.account_transfers.index');
    }

    // List AJAX for DataTable
    public function listAjax(Request $request)
    {
        $draw   = (int) $request->input('draw');
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $branchId = current_branch_id();
        abort_if(! $branchId, 422, 'Please select a branch');

        // 🔑 voucher_type for account transfer
        $voucherTypeId = VoucherType::idByCode('TRANSFER');

        $base = JournalEntry::query()
            ->with([
                'branch:id,name',
                'createdBy:id,name',
                'lines.account:id,name',
            ])
            ->where('voucher_type_id', $voucherTypeId)
            ->where('branch_id', $branchId);

        $total = (clone $base)->count();

        $rows = $base
            ->orderByDesc('entry_date')
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        $sl   = $start + 1;

        foreach ($rows as $je) {

            $from = $je->lines->firstWhere('credit', '>', 0)?->account?->name ?? '-';
            $to   = $je->lines->firstWhere('debit', '>', 0)?->account?->name ?? '-';

            $amount = $je->lines->sum('debit');

            $data[] = [
                $sl++,
                e($je->voucher_no),
                e($from),
                e($to),
                number_format($amount, 2),
                e($je->branch?->name ?? '-'),
                e($je->createdBy?->name ?? '-'),
                $je->entry_date->format('Y-m-d'),
                '<a href="#"
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    title="View Transfer Details"
                    data-size="lg"
                    data-ajax-modal="' . route('accounts.vouchers.view', $je->id) . '">
                    <iconify-icon icon="mdi:eye" class="menu-icon text-lg"></iconify-icon>
                </a>',
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $total,
            'aaData'               => $data,
        ]);
    }

    /**
     * Show transfer modal / page
     */
    public function create(Request $request)
    {
        $branchId = current_branch_id();

        $fromAccounts = Account::query()
            ->join('branch_accounts', 'branch_accounts.account_id', '=', 'accounts.id')
            ->where('branch_accounts.branch_id', $branchId)
            ->where('branch_accounts.is_active', 1)
            ->where('accounts.is_active', 1)
            ->select(
                'accounts.id',
                'accounts.name',
                'branch_accounts.is_default'
            )
            ->orderByDesc('branch_accounts.is_default') // default first
            ->get();

        $toAccounts = BranchAccount::query()
            ->join('accounts', 'accounts.id', '=', 'branch_accounts.account_id')
            ->join('branches', 'branches.id', '=', 'branch_accounts.branch_id')
            ->where('branch_accounts.is_active', 1)
            ->where('accounts.is_active', 1)
            ->select(
                'branch_accounts.id as branch_account_id',
                'branch_accounts.branch_id',
                'branch_accounts.is_default',
                'accounts.id as account_id',
                'accounts.name as account_name',
                'branches.name as branch_name'
            )
            ->orderByRaw('CASE WHEN branch_accounts.branch_id = ? THEN 0 ELSE 1 END', [$branchId])
            ->orderByDesc('branch_accounts.is_default')
            ->orderBy('branches.name')
            ->orderBy('accounts.name')
            ->get();

        return view(
            'backend.modules.account.account_transfers.create',
            compact('fromAccounts', 'toAccounts')
        );
    }

    /**
     * Store transfer
     */
    public function store(Request $request)
    {
        $branchId = current_branch_id();

        $data = $request->validate([
            'transfer_date'        => 'required|date',
            'from_account_id'      => 'required|exists:accounts,id',
            'to_branch_account_id' => 'required|exists:branch_accounts,id',
            'amount'              => 'required|numeric|min:0.01',
            'note'                => 'nullable|string|max:255',
        ]);

        // 🔐 ensure from-account belongs to current branch
        $isValidFrom = BranchAccount::where('branch_id', $branchId)
            ->where('account_id', $data['from_account_id'])
            ->exists();

        if (! $isValidFrom) {
            return response()->json([
                'message' => 'Invalid source account for this branch.',
            ], 422);
        }

        $toBranchAccount = BranchAccount::query()
            ->join('accounts', 'accounts.id', '=', 'branch_accounts.account_id')
            ->where('branch_accounts.id', $data['to_branch_account_id'])
            ->where('branch_accounts.is_active', 1)
            ->where('accounts.is_active', 1)
            ->select(
                'branch_accounts.id',
                'branch_accounts.branch_id',
                'branch_accounts.account_id'
            )
            ->first();

        if (! $toBranchAccount) {
            return response()->json([
                'message' => 'Invalid destination account for this branch.',
            ], 422);
        }

        abort_if(
            $request->from_account_id == $toBranchAccount->account_id && $branchId == $toBranchAccount->branch_id,
            422,
            'From and To account cannot be same'
        );

        return DB::transaction(function () use ($data, $branchId, $toBranchAccount) {

            /* -----------------------------
             | 1️⃣ Journal Entry
             -----------------------------*/
            $journal = JournalEntry::create([
                'voucher_no'      => generateVoucherNo('ACC-TRF'),
                'voucher_type_id' => VoucherType::idByCode('TRANSFER'),
                'branch_id'       => $branchId,
                'fiscal_year_id'  => currentFiscalYear()->id,
                'entry_date'      => $data['transfer_date'],
                'narration'       => 'Account Transfer',
                'created_by'      => auth()->id(),
            ]);

            // Debit → To Account
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $toBranchAccount->account_id,
                'branch_id'        => $toBranchAccount->branch_id,
                'debit'            => $data['amount'],
                'credit'           => 0,
            ]);

            // Credit → From Account
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id'       => $data['from_account_id'],
                'branch_id'        => $branchId,
                'debit'            => 0,
                'credit'           => $data['amount'],
            ]);

            /* -----------------------------
             | 2️⃣ Account Transfer Record
             -----------------------------*/
            AccountTransfer::create([
                'branch_id'        => $branchId,
                'from_account_id'  => $data['from_account_id'],
                'to_account_id'    => $toBranchAccount->account_id,
                'amount'           => $data['amount'],
                'transfer_date'    => $data['transfer_date'],
                'note'             => $data['note'] ?? null,
                'journal_entry_id' => $journal->id,
                'created_by'       => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'msg'     => 'Account transfer completed successfully.',
            ]);
        });
    }

}
