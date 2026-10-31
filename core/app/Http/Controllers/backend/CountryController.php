<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class CountryController extends Controller
{
     public function index()
    {
        return view('backend.modules.countries.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Country::query()->select(['id', 'name']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
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
                    data-ajax-modal="' . route('country.countries.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-country-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('country.countries.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $b->id,
                $nameCol,
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
        return view('backend.modules.countries.create_modal'); // partial only
    }
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            
        ]);

         $country = Country::create([
             'name'      => ucwords($data['name']),
      
        ]);

        return response()->json(['ok' => true, 'msg' => 'Branch created', 'id' => $country->id]);
    }
    public function show(Country $country)
    {

        return response()->json([
            'id'        => $country->id,
            'name'      => $country->name,
            
        ]);
    }
    public function editModal(Country $country)
    {
        return view('backend.modules.countries.edit_modal', compact('country'));
    }
    public function update(Request $req, Country $country)
    {
        $data = $req->validate([
            'name' => ['required', 'string', 'max:150'],
        
        ]);

        $country->name  = ucwords($data['name']);
  
        $country->save();

        return response()->json(['ok' => true, 'msg' => 'country updated']);
    }

     public function destroy(Country $country)
    {
        $inUse = DB::table('users')->where('branch_id', $country->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This country has {$inUse} user(s). Reassign them first.",
            ], 422);
        }

        // DB::table('branch_business')->where('branch_id', $branch->id)->delete();

        $country->delete();

        return response()->json(['ok' => true, 'msg' => 'Country deleted']);
    }


}
