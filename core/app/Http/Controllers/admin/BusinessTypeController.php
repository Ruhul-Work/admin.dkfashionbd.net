<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\backend\BusinessType;


class BusinessTypeController extends Controller
{
    public function index()
    {
        return view('backend.modules.btypes.index'); 
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 1);
        $orderDir  = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $searchVal = trim($request->input('search.value', ''));

        $base  = BusinessType::query()->select(['id', 'name']);
        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where('name', 'like', "%{$searchVal}%");
        }
        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'name';
        $rows     = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
            <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-success-focus text-success-main AjaxModal"
               data-ajax-modal="' . route('org.btypes.editModal', $r->id) . '"
               data-size="sm"
               data-onsuccess="BTypesIndex.onSaved"
               title="Edit">
               <iconify-icon icon="lucide:edit"></iconify-icon>
            </a>
            <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-btype-del"
               data-id="' . $r->id . '"
               data-url="' . route('org.btypes.destroy', $r->id) . '"
               title="Delete">
               <iconify-icon icon="mdi:delete"></iconify-icon>
            </a>
        </div>';

            $data[] = [
                $r->id,
                e($r->name),
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
        return view('backend.modules.btypes.create_modal'); // simple name field
    }

    public function editModal(BusinessType $type)
    {
        return view('backend.modules.btypes.edit_modal', compact('type'));
    }

    public function store(Request $req)
    {
        $data = $req->validate(['name' => 'required|string|max:150|unique:business_types,name']);
        $type = BusinessType::create($data);
        return response()->json(['ok' => true, 'msg' => 'Type created', 'id' => $type->id]);
    }

    public function update(Request $req, BusinessType $type)
    {
        $data = $req->validate(['name' => 'required|string|max:150|unique:business_types,name,' . $type->id]);
        $type->update($data);
        return response()->json(['ok' => true, 'msg' => 'Type updated']);
    }

    public function destroy(BusinessType $type)
    {
        $type->delete();
        return response()->json(['ok' => true, 'msg' => 'Type deleted']);
    }
     public function select2(Request $r)
    {
        
        $q = trim($r->input('q', ''));
        $type = $r->type;
        $base = BusinessType::query();
      

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
