<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiscalYearController extends Controller
{
    public function index()
    {
        return view('backend.modules.fiscal_years.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'start_date', 'end_date', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = FiscalYear::query()->select(['id', 'name', 'start_date', 'end_date', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%");
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
                    data-ajax-modal="' . route('fiscal-years.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="FiscalYearsIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-fiscal-year-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('fiscal-years.destroy', $b->id) . '"
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
                $b->start_date ? $b->start_date->format('Y-m-d') : '-',
                $b->end_date ? $b->end_date->format('Y-m-d') : '-',
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
        return view('backend.modules.fiscal_years.create_modal');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'       => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'is_active'  => ['required', 'integer'],
        ]);

        if ($data['is_active'] == 1) {
            FiscalYear::where('is_active', 1)->update(['is_active' => 0]);
        }

        $fiscalYear = FiscalYear::create($data);

        return response()->json(['ok' => true, 'msg' => 'Fiscal Year created', 'id' => $fiscalYear->id]);
    }

    public function editModal(FiscalYear $fiscalYear)
    {
        return view('backend.modules.fiscal_years.edit_modal', compact('fiscalYear'));
    }

    public function update(Request $req, FiscalYear $fiscalYear)
    {
        $data = $req->validate([
            'name'       => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'is_active'  => ['required', 'integer'],
        ]);

        if ($data['is_active'] == 1) {
            FiscalYear::where('id', '!=', $fiscalYear->id)->where('is_active', 1)->update(['is_active' => 0]);
        }

        $fiscalYear->update($data);

        return response()->json(['ok' => true, 'msg' => 'Fiscal Year updated']);
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        $fiscalYear->delete();
        return response()->json(['ok' => true, 'msg' => 'Fiscal Year deleted']);
    }

    public function select2(Request $r)
    {
        $q = trim($r->input('q', ''));
        $base = FiscalYear::query();

        if ($q !== '') {
            $base->where('name', 'like', "%{$q}%");
        }

        $items = $base->orderBy('id', 'desc')
            ->limit(20)->get(['id', 'name']);

        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->name
            ])
        ]);
    }
}
