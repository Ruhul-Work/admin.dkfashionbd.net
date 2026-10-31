<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use App\Models\backend\AccountType;
use App\Models\backend\JournalEntryLine;
use App\Support\BranchScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        // Guard: Only authenticated users can access
        // Branch filtering will be applied in listAjax
        return view('backend.modules.account.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['name', 'account_type_id'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Account::query()
            ->with([
                'type:id,name',
                'branchAccounts' => function($query) {
                    $query->where('is_active', 1);
                }
            ])
            ->where('is_active', 1);

        // Branch filtering
        $branchAll = BranchScope::isAll();
        $branchId = current_branch_id();

        if (!$branchAll && $branchId) {
            $base->whereHas('branchAccounts', function($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                      ->where('is_active', 1);
            });
        }

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where('name', 'like', "%{$searchVal}%");
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'name';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];

        $sn = $start + 1;
        foreach ($rows as $r) {

            $actions = '
            <div class="d-inline-flex justify-content-end gap-1 w-100">

                <button
                    class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-info-focus text-info-main btn-load-balance"
                    data-account-id="' . $r->id . '"
                    title="Load Balance">
                    <iconify-icon icon="mdi:reload"></iconify-icon>
                </button>

                <a href="#"
                class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-success-focus text-success-main AjaxModal"
                data-ajax-modal="' . route('accounts.editModal', $r->id) . '"
                data-size="lg"
                data-onsuccess="AccountsIndex.onSaved"
                title="Edit">
                <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>

                <a href="#"
                class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-danger-focus text-danger-main btn-account-del"
                data-url="' . route('accounts.destroy', $r->id) . '"
                title="Disable">
                <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>

            </div>';

            $data[] = [
                $sn++,
                e($r->name),
                e($r->type?->name ?? '-'),
                e($r->bank_name ?? '-'),
                e($r->bank_account_no ?? '-'),
                e($r->bank_details ?? '-'),
                // 🔹 Balance placeholder
                '<span class="account-balance" data-id="' . $r->id . '">--</span>',
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

    public function createModal()
    {
        // Guard: Only super admin can create accounts
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $types = AccountType::orderBy('name')->get();
        return view('backend.modules.account.modal.create', compact('types'));
    }

    public function store(Request $request)
    {
        // Guard: Only super admin can create accounts
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name'            => 'required|string|max:100|unique:accounts,name',
            'account_type_id' => 'required|exists:account_types,id',
            'bank_name'       => 'nullable|string|max:150',
            'bank_account_no' => 'nullable|string|max:150',
            'bank_details'    => 'nullable|string',
            'allow_negative'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {

            Account::create([
                'name'            => $request->name,
                'account_type_id' => $request->account_type_id,
                'description'     => null,
                'currency'        => 'BDT',
                'bank_name'       => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'bank_details'    => $request->bank_details,
                'allow_negative'  => $request->allow_negative ? 1 : 0,
                'is_active'       => 1, // ✅ always active on create
            ]);

        });

        return response()->json(['success' => true]);
    }

    public function editModal(Account $account)
    {
        // Guard: Check if user can access this account
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $branchId = $user->branch_id;
            $hasAccess = $account->branchAccounts()
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Unauthorized');
            }
        }

        $types = AccountType::orderBy('name')->get();

        return view('backend.modules.account.modal.edit', compact('account', 'types'));
    }

    // public function update(Request $request, Account $account)
    // {
    //     $request->validate([
    //         'name'            => 'required|string|max:100',
    //         'account_type_id' => 'required|exists:account_types,id',
    //         'opening_balance' => 'nullable|numeric',
    //     ]);

    //     DB::transaction(function () use ($request, $account) {

    //         // 1️⃣ Update account basic info
    //         $account->update([
    //             'name'            => $request->name,
    //             'account_type_id' => $request->account_type_id,
    //             'bank_name'       => $request->bank_name,
    //             'bank_account_no' => $request->bank_account_no,
    //             'bank_details'    => $request->bank_details,
    //             'allow_negative'  => $request->allow_negative ? 1 : 0,
    //         ]);

    //         // 2️⃣ Update opening balance (current branch + fiscal year)
    //         $fiscalYear = currentFiscalYear();
    //         if (! $fiscalYear) {
    //             throw new \Exception('No active fiscal year found.');
    //         }

    //         if ($request->filled('opening_balance')) {
    //             OpeningBalance::updateOrCreate(
    //                 [
    //                     'branch_id'      => auth()->user()->branch_id,
    //                     'account_id'     => $account->id,
    //                     'fiscal_year_id' => $fiscalYear->id,
    //                 ],
    //                 [
    //                     'amount' => $request->opening_balance,
    //                 ]
    //             );
    //         }
    //     });

    //     return response()->json(['success' => true]);
    // }

    public function update(Request $request, Account $account)
    {
        // Guard: Check if user can access this account
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $branchId = $user->branch_id;
            $hasAccess = $account->branchAccounts()
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Unauthorized');
            }
        }

        $request->validate([
            'name'            => 'required|string|max:100|unique:accounts,name,' . $account->id,
            'account_type_id' => 'required|exists:account_types,id',
            'bank_name'       => 'nullable|string|max:150',
            'bank_account_no' => 'nullable|string|max:150',
            'bank_details'    => 'nullable|string',
            'allow_negative'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $account) {

            $account->update([
                'name'            => $request->name,
                'account_type_id' => $request->account_type_id,
                'bank_name'       => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'bank_details'    => $request->bank_details,
                'allow_negative'  => $request->allow_negative ? 1 : 0,
            ]);

        });

        return response()->json(['success' => true]);
    }

    public function destroy(Account $account)
    {
        // Guard: Check if user can access this account
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $branchId = $user->branch_id;
            $hasAccess = $account->branchAccounts()
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Unauthorized');
            }
        }

        // 🔒 Rule 1: Already disabled হলে কিছু করবে না
        if (! $account->is_active) {
            return response()->json([
                'message' => 'Account already disabled.',
            ], 422);
        }

        // 🔒 Rule 2: Journal entry থাকলে delete/disable block
        $hasJournal = JournalEntryLine::where('account_id', $account->id)->exists();

        if ($hasJournal) {
            return response()->json([
                'message' => 'This account has accounting transactions. It cannot be deleted.',
            ], 422);
        }

        // 🔒 Rule 3: Opening balance থাকলেও safe → disable
        $account->update([
            'is_active' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account disabled successfully.',
        ]);
    }

    // global balance (no branch filter)
    public function balance(Account $account)
    {
        $user = auth()->user();

        // Guard: Check if user can access this account
        if (!$user->isSuperAdmin()) {
            $branchId = $user->branch_id;
            $hasAccess = $account->branchAccounts()
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Unauthorized');
            }
        }

        // 🔐 Optional: inactive account block
        abort_if(! $account->is_active, 404, 'Account not active');

        /*
        |--------------------------------------------------------------------------
        | GLOBAL BALANCE (no branch filter)
        |--------------------------------------------------------------------------
        */

        $debit = DB::table('journal_entry_lines')
            ->where('account_id', $account->id)
            ->sum('debit');

        $credit = DB::table('journal_entry_lines')
            ->where('account_id', $account->id)
            ->sum('credit');

        $balance = round($debit - $credit, 2);

        return response()->json([
            'balance' => number_format($balance, 2),
        ]);
    }

    // branch-wise balance
    public function branchBalance(Account $account)
    {
        $user = auth()->user();

        // Guard: Check if user can access this account
        if (!$user->isSuperAdmin()) {
            $branchId = $user->branch_id;
            $hasAccess = $account->branchAccounts()
                ->where('branch_id', $branchId)
                ->where('is_active', 1)
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Unauthorized');
            }
        }

        $branchId = $user->isSuperAdmin()
            ? current_branch_id() // must select branch
            : $user->branch_id;

        abort_if(! $branchId, 422, 'Please select a branch');

        // optional: ensure account is assigned to this branch
        $assigned = DB::table('branch_accounts')
            ->where('branch_id', $branchId)
            ->where('account_id', $account->id)
            ->exists();

        abort_if(! $assigned, 403, 'Account not assigned to this branch');

        $debit = DB::table('journal_entry_lines')
            ->where('account_id', $account->id)
            ->where('branch_id', $branchId)
            ->sum('debit');

        $credit = DB::table('journal_entry_lines')
            ->where('account_id', $account->id)
            ->where('branch_id', $branchId)
            ->sum('credit');

        $balance = round($debit - $credit, 2);

        return response()->json([
            'balance' => number_format($balance, 2),
        ]);
    }

}
