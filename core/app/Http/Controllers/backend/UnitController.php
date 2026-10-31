<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{

    public function index()
    {
        return view('backend.modules.unit.index');
    }

    // DataTables (exactly like Branches' response keys)
    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code', 'precision', 'is_active', 'created_at'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Unit::query()->select(['id', 'name', 'code', 'precision', 'is_active', 'created_at']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $u) {
            $nameCol = '<strong>' . e($u->name) . '</strong>';
            $codeCol = '<code>' . e($u->code) . '</code>';

            $active = $u->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('units.editModal', $u->id) . '"
                    data-size="md"
                    data-onsuccess="UnitsIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-danger-focus text-danger-main btn-unit-delete"
                    data-id="' . $u->id . '"
                    data-url="' . route('units.destroy', $u->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $u->id,
                $nameCol,
                $codeCol,
                (int) $u->precision,
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

    // AjaxModal: create form
    public function createModal()
    {
        return view('backend.modules.unit.modal', [
            'unit'   => null,
            'action' => route('units.store'),
            'method' => 'POST',
            'title'  => 'Add Unit',
        ]);
    }

    // AjaxModal: edit form
    public function editModal(Unit $unit)
    {
        return view('backend.modules.unit.modal', [
            'unit'   => $unit,
            'action' => route('units.update', $unit->id),
            'method' => 'PUT',
            'title'  => 'Edit Unit',
        ]);
    }

    // GET /units/quick
    public function quick()
    {
        return response()->json(
            Unit::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'code', 'precision'])
        );
    }

    // GET /api/units/{unit}
    public function show(Unit $unit)
    {
        return response()->json($unit);
    }

    // POST /api/units
    public function store(Request $request)
    {
        $val = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:16|unique:units,code',
            'precision' => 'required|integer|min:0|max:6',
            'is_active' => 'sometimes|boolean',
        ]);
        $val['is_active'] = $request->boolean('is_active');

        $unit = Unit::create($val);

        return response()->json(['success' => true, 'msg' => 'Unit created', 'id' => $unit->id]);
    }

    // Web update (AjaxModal submit)
    public function update(Request $request, Unit $unit)
    {
        $val = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:16|unique:units,code,' . $unit->id,
            'precision' => 'required|integer|min:0|max:6',
            'is_active' => 'sometimes|boolean',
        ]);
        $val['is_active'] = $request->boolean('is_active');

        $unit->update($val);
        return response()->json(['success' => true, 'msg' => 'Unit updated']);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete(); 
        return response()->json(['success' => true, 'msg' => 'Unit deleted']);
    }

    public function select2(Request $r)
    {

        $q = trim($r->input('q', ''));
        $base = Unit::query()->where('is_active', 1);


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
