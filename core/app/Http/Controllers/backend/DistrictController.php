<?php

namespace App\Http\Controllers\backend;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\backend\District;
use App\Models\backend\Division;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
     public function index()
    {
        return view('backend.modules.districts.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['district_id', 'district_name', 'district_bn_name', 'district_division_id', 'district_lat', 'district_lon','district_url'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = District::query()->select(['district_id', 'district_name', 'district_bn_name', 'district_division_id', 'district_lat', 'district_lon','district_url']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('district_name', 'like', "%{$searchVal}%")
                    ->orWhere('district_bn_name', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'district_id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->district_name) . '</strong>';

            // $active = $b->is_active
            //     ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
            //     : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('district.districts.editModal', $b->district_id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->district_id . '"
                    data-url="' . route('district.districts.destroy', $b->district_id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $b->district_id,
                $nameCol,
                $bangla_name = $b->district_bn_name,
                $division = $b->district_division->name,
                $latitude = $b->district_lat,
                  $longitude = $b->district_lon,
                  $url = $b->district_url,
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
        $divisions = Division::select('id','name')->get();
                                                              // @perm গার্ড চাইলে দিন
        return view('backend.modules.districts.create_modal',compact('divisions')); // partial only
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'district_name'      => ['required', 'string', 'max:150', 'unique:districts,district_name'],
            'district_bn_name'      => ['required', 'string', 'max:50', 'unique:districts,district_bn_name'],
            'district_division_id' =>['required','integer'],
            'district_lat'     => ['nullable', 'string', 'max:50'],
            'district_lon'     => ['nullable', 'string', 'max:191'],
            'district_url'   => ['nullable', 'string', 'max:500'],
           
        ]);

         $district = District::create([
            'district_division_id' => $data['district_division_id'],
             'district_name'      => ucwords($data['district_name']),
            'district_bn_name'      => strtoupper($data['district_bn_name']),
            'district_lat'     => $data['district_lat'] ?? null,
            'district_lon'     => $data['district_lon'] ?? null,
            'district_url'   => $data['district_url'] ?? null,
         
        ]);

        return response()->json(['ok' => true, 'msg' => 'District created', 'id' => $district->id]);
    }

    public function editModal(District $district)
    {
        $divisions = Division::select('id', 'name')->get();
        $current_division = Division::where('id',$district->district_division_id)->first();
        return view('backend.modules.districts.edit_modal', compact('district','divisions','current_division'));
    }

    public function show(District $district)
    {

        return response()->json([
            'district_id'        => $district->district_id,
            'district_name'      => $district->district_name,
            'district_bn_name'      => $district->district_bn_name,
            'division'     => $district->district_division->name,
            'district_lat'     => $district->district_lat,
            'district_lon'   => $district->district_lon,
            'district_url' => $district->district_url,
           
        ]);
    }

    public function update(Request $req, District $district)
    {
             $data = $req->validate([
            'district_division_id' =>['required','integer'],
            'district_name'    => ['required', 'string', 'max:150'],
            'district_bn_name' => ['nullable', 'string', 'max:50'],
            'district_lat'     => ['nullable', 'string', 'max:50'],
            'district_lon'     => ['nullable', 'string', 'max:191'],
            'district_url'   => ['nullable', 'string', 'max:500'],
           
        ]);

        $district->district_name      = ucwords($data['district_name']);
        $district->district_bn_name      = $data['district_bn_name'];
        $district->district_division_id     = $data['district_division_id'] ;
        $district->district_lat     = $data['district_lat'] ?? null;
        $district->district_lon   = $data['district_lon'] ?? null;
        $district->district_url = $data['district_url'] ?? null;
        $district->save();

        return response()->json(['ok' => true, 'msg' => 'District updated']);
    }

    public function destroy(District $district)
    {
        $inUse = DB::table('upazilas')->where('upazila_district_id', $district->district_id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This district has {$inUse} upazila(s). Reassign them first.",
            ], 422);
        }

        // DB::table('branch_business')->where('branch_id', $district->district_id)->delete();

        $district->delete();

        return response()->json(['ok' => true, 'msg' => 'District deleted']);
    }
}
