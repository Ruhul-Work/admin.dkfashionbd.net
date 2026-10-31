<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('backend.modules.warehouse.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code', 'branch_id', 'type', 'is_default', 'is_active', 'created_at'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));
        $branchId  = $request->input('branch_id');

        $base = Warehouse::query()
            ->with('branch:id,name')
            ->select(['id', 'name', 'code', 'branch_id', 'type', 'is_default', 'is_active', 'created_at']);

        if ($branchId) {
            $base->where('branch_id', $branchId);
        }

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%");
            });
        }

        $total    = (clone $base)->count();
        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $w) {
            $nameCol = '<strong>' . e($w->name) . '</strong><br><code>' . e($w->code) . '</code>';
            $branch  = $w->branch?->name ?? '—';
            $type    = ucfirst($w->type ?? 'store');

            $isDefault = $w->is_default
                ? '<span class="badge text-sm fw-semibold border border-warning-600 text-warning-600 bg-transparent px-20 py-9 radius-4 text-white">Default</span>'
                : '<span class="badge text-sm fw-semibold border border-danger-600 text-danger-600 bg-transparent px-20 py-9 radius-4 text-white">No</span>';

            $active = $w->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('inventory.warehouses.editModal', $w->id) . '"
                    data-size="md"
                    data-onload="WarehousesIndex.onLoad"
                    data-onsuccess="WarehousesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-focus text-danger-main btn-warehouse-delete"
                    data-url="' . route('inventory.warehouses.destroy', $w->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $w->id,
                $nameCol,
                e($branch),
                e($type),
                $isDefault,
                $active,
                $actions,
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $total,
            'aaData'               => $data,
        ]);
    }

    public function createModal()
    {
        return view('backend.modules.warehouse.modal', [
            'warehouse' => null,
            'action'    => route('inventory.warehouses.store'),
            'method'    => 'POST',
            'title'     => 'Add Warehouse',
        ]);
    }

    public function editModal(Warehouse $warehouse)
    {
        return view('backend.modules.warehouse.modal', [
            'warehouse' => $warehouse,
            'action'    => route('inventory.warehouses.update', $warehouse->id),
            'method'    => 'PUT',
            'title'     => 'Edit Warehouse',
        ]);
    }

    public function store(Request $r)
    {
        $val = $r->validate([
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:32', 'unique:warehouses,code'],
            'branch_id'  => ['nullable', 'exists:branches,id'],
            'type'       => ['nullable', Rule::in(['store', 'showroom', 'returns', 'virtual'])],
            'is_default' => ['sometimes', 'boolean'],
            'is_active'  => ['sometimes', 'boolean'],
            'phone'      => ['nullable', 'string', 'max:32'],
            'email'      => ['nullable', 'email', 'max:120'],
            'address'    => ['nullable', 'string', 'max:255'],
        ]);

        $val['is_default'] = $r->boolean('is_default');
        $val['is_active']  = $r->boolean('is_active');

        // (optional) প্রতি branch-এ single default enforce (app-level)
        if (! empty($val['is_default']) && ! empty($val['branch_id'])) {
            Warehouse::where('branch_id', $val['branch_id'])->update(['is_default' => false]);
        }

        Warehouse::create($val);
        return response()->json(['success' => true, 'msg' => 'Warehouse created']);
    }

    public function update(Request $r, Warehouse $warehouse)
    {
        $val = $r->validate([
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:32', 'unique:warehouses,code,' . $warehouse->id],
            'branch_id'  => ['nullable', 'exists:branches,id'],
            'type'       => ['nullable', Rule::in(['store', 'showroom', 'returns', 'virtual'])],
            'is_default' => ['sometimes', 'boolean'],
            'is_active'  => ['sometimes', 'boolean'],
            'phone'      => ['nullable', 'string', 'max:32'],
            'email'      => ['nullable', 'email', 'max:120'],
            'address'    => ['nullable', 'string', 'max:255'],
        ]);
        $val['is_default'] = $r->boolean('is_default');
        $val['is_active']  = $r->boolean('is_active');

        if (! empty($val['is_default']) && ! empty($val['branch_id'])) {
            Warehouse::where('branch_id', $val['branch_id'])
                ->where('id', '!=', $warehouse->id)
                ->update(['is_default' => false]);
        }

        $warehouse->update($val);
        return response()->json(['success' => true, 'msg' => 'Warehouse updated']);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete(); // soft delete
        return response()->json(['success' => true, 'msg' => 'Warehouse deleted']);
    }

    // Select2 endpoint
    // public function select2(Request $r)
    // {
    //     $q        = trim($r->input('q', ''));
    //     $branchId = $r->input('branch_id');

    //     $base = Warehouse::query()->where('is_active', 1);
    //     if ($branchId) {
    //         $base->where('branch_id', $branchId);
    //     }

    //     if ($q !== '') {
    //         $base->where(function ($x) use ($q) {
    //             $x->where('name', 'like', "%{$q}%")
    //                 ->orWhere('code', 'like', "%{$q}%");
    //         });
    //     }

    //     $items = $base->orderByDesc('is_default')->orderBy('name')
    //         ->limit(20)->get(['id', 'name', 'code']);

    //     return response()->json([
    //         'results' => $items->map(fn($w) => [
    //             'id'   => $w->id,
    //             'text' => $w->name . ' (' . $w->code . ')',
    //         ]),
    //     ]);
    // }

    public function select2(Request $r)
    {
        $q = Warehouse::query()
            ->select('id', 'name', 'code', 'branch_id')
            ->where('is_active', 1);

        // current branch guard
        if (function_exists('current_branch_id') && ! $r->has('ignore_branch')) {
            $curr = current_branch_id(); // e.g. session('branch_id')
            if ($curr !== null) {
                $q->where('branch_id', $curr);
            }
            // else: superadmin mode হলে সব ব্রাঞ্চ দেখাতে চাইলে এই whereটি skip থাকবে
        }

        if ($term = $r->get('q')) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%");
        }

        return response()->json([
            'results' => $q->limit(20)->get()->map(fn($w) => [
                'id'   => $w->id,
                'text' => $w->name . ' (' . $w->code . ')',
            ]),
        ]);

    }

    public function showForAjax($id)
    {
        $w = Warehouse::with('branch')->findOrFail($id);
        return response()->json([
            'id'          => $w->id,
            'name'        => $w->name,
            'branch_id'   => $w->branch_id,
            'branch_name' => optional($w->branch)->name,
        ]);
    }

}
