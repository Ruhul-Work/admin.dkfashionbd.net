<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\District;
use App\Models\backend\Upazila;
use Illuminate\Http\Request;

class UpazilaController extends Controller
{
     public function index()
    {
        return view('backend.modules.upazilas.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'upazila_name', 'upazila_bn_name', 'upazila_district_id', 'upazila_url'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Upazila::query()->select(['id', 'upazila_name', 'upazila_bn_name', 'upazila_district_id', 'upazila_url']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('upazila_name', 'like', "%{$searchVal}%")
                    ->orWhere('upazila_bn_name', 'like', "%{$searchVal}%");
                    // ->orWhere('upazila_district', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->upazila_name) . '</strong>';

            // $active = $b->is_active
            //     ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
            //     : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('upazila.upazilas.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('upazila.upazilas.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $b->id,
                $nameCol,
                $bangla_name = $b->upazila_bn_name,
                $district = $b->upazila_district->district_name,
                  $url = $b->upazila_url,
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
        $districts = District::select('district_id','district_name')->get();
                                                              // @perm গার্ড চাইলে দিন
        return view('backend.modules.upazilas.create_modal',compact('districts')); // partial only
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'upazila_name'      => ['required', 'string', 'max:150', 'unique:upazilas,upazila_name'],
            'upazila_bn_name'      => ['required', 'string', 'max:50', 'unique:upazilas,upazila_bn_name'],
            'upazila_district_id' =>['required','integer'],
            'upazila_url'   => ['nullable', 'string', 'max:500'],
           
        ]);

         $upazila = Upazila::create([
            'upazila_district_id' => $data['upazila_district_id'],
             'upazila_name'      => ucwords($data['upazila_name']),
            'upazila_bn_name'      => strtoupper($data['upazila_bn_name']),
            'upazila_url'   => $data['upazila_url'] ?? null,
         
        ]);

        return response()->json(['ok' => true, 'msg' => 'Upazila created', 'id' => $upazila->id]);
    }

    public function editModal(Upazila $upazila)
    {
        $districts = District::select('district_id', 'district_name')->get();
        $current_district = District::where('district_id',$upazila->upazila_district_id)->first();
        return view('backend.modules.upazilas.edit_modal', compact('upazila','districts','current_district'));
    }

    public function show(Upazila $upazila)
    {

        return response()->json([
            'id'        => $upazila->id,
            'upazila_name'      => $upazila->district_name,
            'upazila_bn_name'      => $upazila->district_bn_name,
            'upazila'     => $upazila->upazila_district->name,
            'upazila_url' => $upazila->upazila_url,
           
        ]);
    }

    public function update(Request $req, Upazila $upazila)
    {
          $data = $req->validate([
            'upazila_name'      => ['required', 'string', 'max:150'],
            'upazila_bn_name'      => ['nullable', 'string', 'max:50'],
            'upazila_district_id' =>['required','integer'],
            'upazila_url'   => ['nullable', 'string', 'max:500'],
           
        ]);

        $upazila->upazila_name      = ucwords($data['upazila_name']);
        $upazila->upazila_bn_name      = $data['upazila_bn_name'];
        $upazila->upazila_district_id     = $data['upazila_district_id'] ;
        $upazila->upazila_url = $data['upazila_url'] ?? null;
        $upazila->save();

        return response()->json(['ok' => true, 'msg' => 'Upazila updated']);
    }

    public function destroy(Upazila $upazila)
    {
        // $inUse = DB::table('users')->where('branch_id', $district->district_id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This district has {$inUse} user(s). Reassign them first.",
        //     ], 422);
        // }

        // DB::table('branch_business')->where('branch_id', $district->district_id)->delete();

        $upazila->delete();

        return response()->json(['ok' => true, 'msg' => 'Upazila deleted']);
    }
}
