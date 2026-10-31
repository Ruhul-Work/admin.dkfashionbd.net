<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\backend\Size;

class SizeController extends Controller
{
     public function index()
    {
        return view('backend.modules.size.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id','name','code','sort','is_active','created_at'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Size::query()->select(['id','name','code','sort','is_active','created_at']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function($q) use ($searchVal){
                $q->where('name','like',"%{$searchVal}%")
                  ->orWhere('code','like',"%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();
        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $s) {
            $nameCol = '<strong>'.e($s->name).'</strong>';
            $codeCol = '<code>'.e($s->code).'</code>';

            $active = $s->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="'.route('sizes.editModal', $s->id).'"
                    data-size="md"
                    data-onsuccess="SizesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-focus text-danger-main btn-size-delete"
                    data-id="'.$s->id.'"
                    data-url="'.route('sizes.destroy', $s->id).'"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $s->id,
                $nameCol,
                $codeCol,
                (int)$s->sort,
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
        return view('backend.modules.size.modal', [
            'size'   => null,
            'action' => route('sizes.store'),
            'method' => 'POST',
            'title'  => 'Add Size',
        ]);
    }

    public function editModal(Size $size)
    {
        return view('backend.modules.size.modal', [
            'size'   => $size,
            'action' => route('sizes.update', $size->id),
            'method' => 'PUT',
            'title'  => 'Edit Size',
        ]);
    }

    public function store(Request $request)
    {
        $val = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:32|unique:sizes,code',
            'sort'      => 'nullable|integer|min:0|max:65535',
            'is_active' => 'sometimes|boolean',
        ]);
        $val['is_active'] = $request->boolean('is_active');
        $val['sort']      = $val['sort'] ?? 0;
        $val['name'] = ucwords($val['name']);
        $val['code'] = strtoupper($val['code']);

        $size = Size::create($val);

        return response()->json(['success'=>true,'msg'=>'Size created','id'=>$size->id]);
    }

    public function update(Request $request, Size $size)
    {
        $val = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:32|unique:sizes,code,'.$size->id,
            'sort'      => 'nullable|integer|min:0|max:65535',
            'is_active' => 'sometimes|boolean',
        ]);
        $val['is_active'] = $request->boolean('is_active');
        $val['sort']      = $val['sort'] ?? 0;
             $val['name'] = ucwords($val['name']);
        $val['code'] = strtoupper($val['code']);

        $size->update($val);
        return response()->json(['success'=>true,'msg'=>'Size updated']);
    }

    public function destroy(Size $size)
    {
        $size->delete();
        return response()->json(['success'=>true,'msg'=>'Size deleted']);
    }

     public function select2(Request $r)
    {

        $q = trim($r->input('q', ''));
        $base = Size::query()->where('is_active', 1);


        if ($q !== '') {
            $base->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%");
            });
        }

        $items = $base->orderBy('id')->orderBy('name')
            ->limit(20)->get(['id', 'code']);


        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->code
            ])
        ]);
    }
}
