<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\AccountType;
use App\Models\backend\Account;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    public function index()
    {
        return view('backend.modules.accountType.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 1);
        $orderDir  = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $searchVal = trim($request->input('search.value', ''));

        $base  = AccountType::query()->select(['id', 'name', 'code']);
        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'name';
        $rows     = $base->orderBy($orderCol, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($rows as $r) {

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">

            <a href="#"
               class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-success-focus text-success-main AjaxModal"
               data-ajax-modal="' . route('account-types.editModal', $r->id) . '"
               data-size="sm"
               data-onsuccess="AccountTypesIndex.onSaved"
               title="Edit">
               <iconify-icon icon="lucide:edit"></iconify-icon>
            </a>

            <a href="#"
               class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-account-type-del"
               data-id="' . $r->id . '"
               data-url="' . route('account-types.destroy', $r->id) . '"
               title="Delete">
               <iconify-icon icon="mdi:delete"></iconify-icon>
            </a>

        </div>';

            $data[] = [
                $r->id,
                e($r->name),
                e($r->code ?? '-'),
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
        return view('backend.modules.accountType.modal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:account_types,name',
            'code' => 'nullable|string|max:20|unique:account_types,code',
        ]);

        AccountType::create($request->only('name', 'code'));

        return response()->json(['success' => true]);
    }

    public function editModal(AccountType $type)
    {
        return view(
            'backend.modules.accountType.modal.edit',compact('type')
        );
    }

    public function update(Request $request, AccountType $type)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:account_types,name,' . $type->id,
            'code' => 'nullable|string|max:20|unique:account_types,code,' . $type->id,
        ]);

        // ❗ Safety rule: if accounts already exist, allow rename but no restriction needed here
        $type->update($request->only('name', 'code'));

        return response()->json(['success' => true]);
    }

    public function destroy(AccountType $type)
    {
        // ❌ If used by accounts, do not delete
        if ($type->accounts()->exists()) {
            return response()->json([
                'message' => 'Cannot delete. Accounts already exist under this type.',
            ], 422);
        }

        $type->delete();
        return response()->json(['success' => true]);
    }
}
