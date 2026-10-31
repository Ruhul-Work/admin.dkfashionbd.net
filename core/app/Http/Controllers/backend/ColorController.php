<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\backend\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
     public function index()
    {
        return view('backend.modules.colors.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code', 'hex', 'sort', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Color::query()->select(['id', 'name', 'code', 'hex', 'sort', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                ->orWhere('code', 'like', "%{$searchVal}%")
                ->orWhere('hex', 'like', "%{$searchVal}%")
                   ;
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->name) . '</strong>';


            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('color.colors.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-color-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('color.colors.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            
            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $data[] = [
                $b->id,
                $nameCol,
                $b->code,
                $b->hex,
                $b->sort,
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
        return view('backend.modules.colors.create_modal'); // partial only
    }
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:150'],
            'hex'      => ['required', 'string', 'max:150'],
            'sort'      => ['required', 'integer'],
            'is_active'      => ['required', 'integer'],
            
        ]);

         $color = Color::create([
             'name'      => strtoupper($data['name']),
             'code' => $data['code'],
             'hex' => $data['hex'],
             'sort' => $data['sort'],
             'is_active' => $data['is_active'],
             
      
        ]);

        return response()->json(['ok' => true, 'msg' => 'Color created', 'id' => $color->id]);
    }
    public function show(Color $color)
    {

        return response()->json([
            'id'        => $color->id,
            'name'      => $color->name,
            
        ]);
    }
    public function editModal(Color $color)
    {
        return view('backend.modules.colors.edit_modal', compact('color'));
    }
    public function update(Request $req, Color $color)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:150'],
            'hex'      => ['required', 'string', 'max:150'],
            'sort'      => ['required', 'integer'],
            'is_active'      => ['required', 'integer'],
            
        
        ]);

        $color->name  = strtoupper($data['name']);
        $color->code = $data['code'];
        $color->hex = $data['hex'];
        $color->sort = $data['sort'];
        $color->is_active = $data['is_active'];
  
        $color->save();

        return response()->json(['ok' => true, 'msg' => 'color updated']);
    }

     public function destroy(Color $color)
    {
        // $inUse = DB::table('users')->where('branch_id', $color->id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This color has {$inUse} user(s). Reassign them first.",
        //     ], 422);
        // }

        // DB::table('branch_business')->where('branch_id', $branch->id)->delete();

        $color->delete();

        return response()->json(['ok' => true, 'msg' => 'color deleted']);
    }

     public function select2(Request $r)
    {

        $q = trim($r->input('q', ''));
        $base = Color::query()->where('is_active', 1);


        if ($q !== '') {
            $base->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%");
            });
        }

        $items = $base->orderBy('id')->orderBy('name')
            ->limit(20)->get(['id', 'name']);


        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->name
            ])
        ]);
    }

}
