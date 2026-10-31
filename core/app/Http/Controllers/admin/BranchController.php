<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\backend\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\backend\BusinessType;




class BranchController extends Controller
{
    public function index()
    {
        return view('backend.modules.branches.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code', 'phone', 'address', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Branch::query()->select(['id', 'name', 'code', 'phone', 'address', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%")
                    ->orWhere('phone', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->name) . '</strong><br><code>' . e($b->code) . '</code>';

            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('org.branches.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('org.branches.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $b->id,
                $nameCol,
                e($b->phone ?? '—'),
                e($b->address ?? '—'),
                $active,
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
        // @perm গার্ড চাইলে দিন
        return view('backend.modules.branches.create_modal'); // partial only
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:50', 'unique:branches,code'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'email'     => ['nullable', 'email', 'max:191'],
            'address'   => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $branch = Branch::create([
            'name'      => ucwords($data['name']),
            'code'      => strtolower($data['code']),
            'phone'     => $data['phone'] ?? null,
            'email'     => $data['email'] ?? null,
            'address'   => $data['address'] ?? null,
            'is_active' => (int) ($data['is_active'] ?? 0),
        ]);

        return response()->json(['ok' => true, 'msg' => 'Branch created', 'id' => $branch->id]);
    }

    public function editModal(Branch $branch)
    {
        return view('backend.modules.branches.edit_modal', compact('branch'));
    }

    public function show(Branch $branch)
    {

        return response()->json([
            'id'        => $branch->id,
            'name'      => $branch->name,
            'code'      => $branch->code,
            'phone'     => $branch->phone,
            'email'     => $branch->email,
            'address'   => $branch->address,
            'is_active' => (int) $branch->is_active,
        ]);
    }

    public function update(Request $req, Branch $branch)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:50', 'unique:branches,code,' . $branch->id],
            'phone'     => ['nullable', 'string', 'max:50'],
            'email'     => ['nullable', 'email', 'max:191'],
            'address'   => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable'],
        ]);

        $branch->name      = ucwords($data['name']);
        $branch->code      = strtolower($data['code']);
        $branch->phone     = $data['phone'] ?? null;
        $branch->email     = $data['email'] ?? null;
        $branch->address   = $data['address'] ?? null;
        $branch->is_active = $req->boolean('is_active');
        $branch->save();

        return response()->json(['ok' => true, 'msg' => 'Branch updated']);
    }

    public function destroy(Branch $branch)
    {
        $inUse = DB::table('users')->where('branch_id', $branch->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This branch has {$inUse} user(s). Reassign them first.",
            ], 422);
        }

        // DB::table('branch_business')->where('branch_id', $branch->id)->delete();

        $branch->delete();

        return response()->json(['ok' => true, 'msg' => 'Branch deleted']);
    }

    public function typesModal(Branch $branch)
    {
        // preselected ids
        $selected = $branch->businessTypes()->pluck('business_types.id')->all();
        // dropdown এর জন্য সব types (cache করতে পারেন)
        $types = BusinessType::orderBy('name')->get(['id', 'name']);
        return view('backend.modules.branches.modals.types', compact('branch', 'types', 'selected'));
    }

    public function typesSync(Request $req, Branch $branch)
    {
        $ids = $req->input('types', []); // array of ids
        $ids = array_map('intval', (array) $ids);
        $branch->businessTypes()->sync($ids);
        return response()->json(['ok' => true, 'msg' => 'Branch types updated']);
    }

    public function select2(Request $req)
    {
        $q = trim($req->get('q', ''));
        $rows = Branch::query()
            ->where('is_active', 1)
            ->when($q, fn($w) => $w->where('name', 'like', "%{$q}%")
                ->orWhere('code', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'code']);

        // select2 format: {id,text}
        return response()->json([
            'results' => $rows->map(fn($b) => [
                'id'   => $b->id,
                'text' => $b->name . ($b->code ? " ({$b->code})" : '')
            ])
        ]);
    }

    

}
