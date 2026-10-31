<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Account;
use App\Models\backend\Branch;
use App\Models\backend\BranchAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchAccountController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        $accounts = Account::where('is_active', 1)
            ->with('type:id,name')
            ->orderBy('name')
            ->get();

        return view(
            'backend.modules.accountBranch.index',
            compact('branches', 'accounts')
        );
    }

    public function assignedAccounts($branchId)
    {
        $rows = BranchAccount::where('branch_id', $branchId)->get();

        return response()->json([
            'account_ids'        => $rows->pluck('account_id'),
            'default_account_id' => $rows->firstWhere('is_default', 1)?->account_id,
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'account_ids'        => 'required|array|min:1',
            'account_ids.*'      => 'exists:accounts,id',
            'default_account_id' => 'required',
        ]);

      
        abort_if(
            ! in_array($request->default_account_id, $request->account_ids),
            422,
            'Default account must be one of assigned accounts'
        );

        DB::transaction(function () use ($request) {

            BranchAccount::where('branch_id', $request->branch_id)->delete();

            foreach ($request->account_ids as $accountId) {
                BranchAccount::create([
                    'branch_id'  => $request->branch_id,
                    'account_id' => $accountId,
                    'is_active'  => 1,
                    'is_default' => ($accountId == $request->default_account_id),
                ]);
            }
        });

        return back()->with('success', 'Branch accounts updated successfully.');
    }
}