<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Division;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    //
       public function index()
    {
        return view('backend.modules.divisions.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'bn_name','url'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Division::query()->select(['id', 'name', 'bn_name','url']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('bn_name', 'like', "%{$searchVal}%")
                    ->orWhere('url', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->name) . '</strong>';

            // $active = $b->is_active
            //     ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
            //     : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('division.divisions.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('division.divisions.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $b->id,
                $nameCol,
                // e($b->phone ?? '—'),
                // e($b->address ??
                $b->bn_name,
                $b->url,
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
        return view('backend.modules.divisions.create_modal'); // partial only
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'bn_name'      => ['required', 'string', 'max:50', 'unique:divisions,bn_name'],
            'url'     => ['nullable', 'string', 'max:50'],
          
        ]);

         $division = Division::create([
             'name'      => ucwords($data['name']),
            'bn_name'      => $data['bn_name'],
            'url'     => $data['url'] ?? null,
           
        ]);

        return response()->json(['ok' => true, 'msg' => 'Division created', 'id' => $division->id]);
    }

    public function editModal(Division $division)
    {
        return view('backend.modules.divisions.edit_modal', compact('division'));
    }

    public function show(Division $division)
    {

        return response()->json([
            'id'        => $division->id,
            'name'      => $division->name,
            'bn_name'      => $division->bn_mame,
            'url' => $division->url,
       
        ]);
    }

    public function update(Request $req, Division $division)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'bn_name'      => ['required', 'string', 'max:50', 'unique:divisions,bn_name,' . $division->id],
            'url'     => ['nullable', 'string', 'max:50'],
        
        ]);

        $division->name      = ucwords($data['name']);
        $division->bn_name      = $data['bn_name'];
        $division->url     = $data['url'] ?? null;
  
        $division->save();

        return response()->json(['ok' => true, 'msg' => 'Division updated']);
    }

    public function destroy(Division $division)
    {
        $inUse = DB::table('districts')->where('district_division_id', $division->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This division has {$inUse} district(s). Reassign them first.",
            ], 422);
        }

        // DB::table('branch_business')->where('branch_id', $branch->id)->delete();

        $division->delete();

        return response()->json(['ok' => true, 'msg' => 'Division deleted']);
    }

}
