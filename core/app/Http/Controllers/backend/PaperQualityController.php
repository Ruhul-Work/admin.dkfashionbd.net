<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\PaperQuality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaperQualityController extends Controller
{
    
    public function index()
    {
        return view('backend.modules.paper_qualities.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = PaperQuality::query()->select(['id', 'name', 'code', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%")
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

            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('paper_quality.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('paper_quality.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $b->id,
                $nameCol,

                $b->code,
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
        return view('backend.modules.paper_qualities.create_modal'); // partial only
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150', 'unique:category_types,name'],
            'code'      => ['required', 'string', 'max:50', 'unique:category_types,code'],
            'is_active'     => ['required', 'integer'],

        ]);

        $PaperQuality = PaperQuality::create([
            'name'      => ucwords($data['name']),
            'code'      => $data['code'],
            'is_active'      => $data['is_active'],

        ]);

        return response()->json(['ok' => true, 'msg' => 'PaperQuality created', 'id' => $PaperQuality->id]);
    }

    public function editModal(PaperQuality $paperQuality)
    {
        return view('backend.modules.paper_qualities.edit_modal', compact('paperQuality'));
    }

    public function show(PaperQuality $PaperQuality)
    {

        return response()->json([
            'id'        => $PaperQuality->id,
            'name'      => $PaperQuality->name,
            'bn_name'      => $PaperQuality->bn_mame,
            'url' => $PaperQuality->url,

        ]);
    }

    public function update(Request $req, PaperQuality $paperQuality)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:50'],
            'is_active'     => ['required', 'integer'],

        ]);

        $paperQuality->name      = ucwords($data['name']);
        $paperQuality->code      = $data['code'];
        $paperQuality->is_active      = $data['is_active'];

        $paperQuality->save();

        return response()->json(['ok' => true, 'msg' => 'PaperQuality updated']);
    }
    public function destroy(PaperQuality $paperQuality)
    {
        // $inUse = DB::table('products')->where('product_type_id', $paperQuality->id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This PaperQuality has {$inUse} product(s). Reassign them first.",
        //     ], 422);
        // }


        $paperQuality->delete();

        return response()->json(['ok' => true, 'msg' => 'PaperQuality deleted']);
    }

      public function select2(Request $r)
    {
        
        $q = trim($r->input('q', ''));
        $base = PaperQuality::query()->where('is_active', 1);
      

        if ($q !== '') {
            $base->where(function($x) use ($q){
                $x->where('name','like',"%{$q}%")
                ;
            });
        }

        $items = $base->orderBy('id')->orderBy('name')
                      ->limit(20)->get(['id','name']);


        return response()->json([
            'results' => $items->map(fn($t)=>[
                'id'   => $t->id,
                'text' => $t->name 
            ])
        ]);
    }

}
