<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\BusinessType;
use App\Models\backend\CompanySetting;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    public function index()
    {
        return view('backend.modules.company_settings.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'code', 'address', 'city', 'country', 'email', 'phone', 'website', 'logo', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = CompanySetting::query()->select(['id', 'name', 'code',  'address', 'city', 'country', 'email', 'phone', 'website', 'logo', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'district_id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->name) . '</strong>';

            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="' . route('company_setting.edit', $b->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main "
                    data-ajax-modal="' . route('company_setting.edit', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('company_setting.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $logo = '<div  style="width:70px"><img src="' . image($b->logo) . '" alt="img"></div>';

            $data[] = [
                $b->id,
                $nameCol,
                $b->code,
                $logo,
                $b->email,
                $b->phone,
                $b->website,
                $b->address,
                $b->city,
                $b->country,

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
    public function create()
    {

        return view('backend.modules.company_settings.create'); // partial only
    }
    public function store(Request $req)
    {
        // dd($req->all());
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:100', 'unique:company_settings,name'],
            'code'      => ['required', 'string', 'max:50', 'unique:company_settings,code'],
            // 'business_type_id'   => ['required', 'integer',],
            'address' => ['required', 'string', 'max:255'],
            'city'   => ['required', 'string', 'max:100'],
            'country'   => ['required', 'string', 'max:100'],
            'email'   => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:50'],
            'website'   => ['required', 'string', 'max:255'],

            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => ['required', 'integer'],

            'facebook_url' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'shipping_charge' => ['nullable', 'integer', 'min:0'],


        ]);


        $imagePath = null;
        if ($req->hasFile('logo')) {
            $imagePath = uploadImage($req->file('logo'), 'company_setting/logos');
        }

        $company_setting = CompanySetting::create([
            'name'      => ucwords($data['name']),
            'code'      => strtoupper($data['code']),
            //'business_type_id'      => $data['business_type_id'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'address'      => $data['address'],
            'city'      => ucwords($data['city']),
            'country'      => ucwords($data['country']),
            'website'      => $data['website'],
            'logo'  => $imagePath,

            'facebook_url' => $data['facebook_url'],
            'instagram_url' => $data['instagram_url'],
            'youtube_url' => $data['youtube_url'],
            'shipping_charge' => $data['shipping_charge'],

            'is_active'     => $data['is_active'] ?? null,

        ]);

        return redirect()->route('company_setting.index')
            ->with('success', 'company setting successfully!');
    }

    public function edit(CompanySetting $companySetting)
    {

        return view('backend.modules.company_settings.edit', compact('companySetting'));
    }
    public function update(Request $req, CompanySetting $companySetting)
    {

        $data = $req->validate([
            'name'      => ['required', 'string', 'max:100'],
            'code'      => ['required', 'string', 'max:50'],
            // 'business_type_id'   => ['required', 'integer'],
            'address'   => ['required', 'string', 'max:255'],
            'city'   => ['required', 'string', 'max:100'],
            'country'   => ['required', 'string', 'max:100'],
            'email'   => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:50'],
            'website'   => ['required', 'string', 'max:255'],

            'facebook_url' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'shipping_charge' => ['nullable', 'integer', 'min:0'],

            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => ['required', 'integer'],


        ]);



        $previousImage = $companySetting->logo;

        if ($req->hasFile('logo')) {
            $imagePath = uploadImage($req->file('logo'), 'company_setting/logos');
            $companySetting->logo = $imagePath;

            if ($previousImage && file_exists($previousImage)) {
                unlink($previousImage);
            }
        }


        $companySetting->name = ucwords($data['name']);
        $companySetting->code = strtoupper($data['code']);
        // $companySetting->business_type_id = $data['business_type_id'] ?? null;
        $companySetting->email = $data['email'] ?? null;
        $companySetting->phone = $data['phone'] ?? null;
        $companySetting->address = $data['address'] ?? null;
        $companySetting->city = ucwords($data['city'] ?? null);
        $companySetting->country = ucwords($data['country'] ?? null);
        $companySetting->website = $data['website'] ?? null;

        $companySetting->facebook_url = $data['facebook_url'] ?? null;
        $companySetting->instagram_url = $data['instagram_url'] ?? null;
        $companySetting->youtube_url = $data['youtube_url'] ?? null;
        $companySetting->shipping_charge = $data['shipping_charge'] ?? 0;

        $companySetting->is_active = $data['is_active'];

        $companySetting->save();

        return redirect()->route('company_setting.index')
            ->with('success', 'Company Setting updated successfully!');
    }

    public function destroy(CompanySetting $companySetting)
    {
        // $inUse = DB::table('products')->where('brand_id', $brand->id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This Brand has {$inUse} product(s). Reassign them first.",
        //     ], 422);
        // }


        $imagePath = $companySetting->logo;



        $companySetting->delete();


        if (isset($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }


        return response()->json(['ok' => true, 'msg' => 'Company Setting deleted']);
    }
}
