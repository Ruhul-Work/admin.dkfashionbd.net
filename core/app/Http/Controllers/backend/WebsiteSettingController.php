<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WebsiteSettingController extends Controller
{
    public function index()
    {
        return view('backend.modules.website_settings.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'logo', 'profile_key', 'company_name', 'email', 'phone', 'created_at'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = WebsiteSetting::query();

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('profile_key', 'like', "%{$searchVal}%")
                  ->orWhere('company_name', 'like', "%{$searchVal}%")
                  ->orWhere('email', 'like', "%{$searchVal}%")
                  ->orWhere('phone', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $item) {
            $logo = $item->logo ? '<img src="' . image($item->logo) . '" width="40" class="rounded">' : 'No Logo';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="' . route('website-setting.website-settings.edit', $item->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-delete"
                    data-id="' . $item->id . '"
                    data-url="' . route('website-setting.website-settings.destroy', $item->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $item->id,
                $logo,
                '<strong>' . e($item->profile_key) . '</strong>',
                e($item->company_name),
                e($item->email),
                e($item->phone),
                $item->created_at->format('Y-m-d'),
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
        return view('backend.modules.website_settings.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'profile_key'  => 'required|string|max:100|unique:website_settings,profile_key',
            'company_name' => 'required|string|max:255',
            'website_url'  => 'nullable|url',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:100',
            'currency_code' => 'nullable|string|max:10',
            'shipping_charge' => 'nullable|numeric',
            'free_shipping_amount' => 'nullable|numeric',
            'address'      => 'nullable|string',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'favicon'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'merchant_qr'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'facebook'     => 'nullable|url',
            'instagram'    => 'nullable|url',
            'twitter'      => 'nullable|url',
            'linkedin'     => 'nullable|url',
            'youtube'      => 'nullable|url',
            'tiktok'       => 'nullable|url',
            'display_video' => 'nullable|mimes:mp4,mov,ogg,qt|max:30000',
            'slider_image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'slider_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'slider_image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'popup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            if ($req->hasFile('logo')) {
                $data['logo'] = uploadImage($req->file('logo'), 'settings');
            }

            if ($req->hasFile('favicon')) {
                $data['favicon'] = uploadImage($req->file('favicon'), 'settings');
            }

            if ($req->hasFile('merchant_qr')) {
                $data['merchant_qr'] = uploadImage($req->file('merchant_qr'), 'settings');
            }

            if ($req->hasFile('display_video')) {
                $data['display_video'] = uploadFile($req->file('display_video'), 'settings');
            }

            if ($req->hasFile('slider_image1')) {
                $data['slider_image1'] = uploadImage($req->file('slider_image1'), 'settings');
            }
            if ($req->hasFile('slider_image2')) {
                $data['slider_image2'] = uploadImage($req->file('slider_image2'), 'settings');
            }
            if ($req->hasFile('slider_image3')) {
                $data['slider_image3'] = uploadImage($req->file('slider_image3'), 'settings');
            }
            if ($req->hasFile('popup_image')) {
                $data['popup_image'] = uploadImage($req->file('popup_image'), 'settings');
            }

            $data['updated_by'] = auth()->id();

            WebsiteSetting::create($data);

            return redirect()->route('website-setting.website-settings.index')->with('success', 'Website settings created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create settings: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(WebsiteSetting $setting)
    {
        return view('backend.modules.website_settings.edit', compact('setting'));
    }

    public function update(Request $req, WebsiteSetting $setting)
    {
        // dd($req->all());
        $data = $req->validate([
            'profile_key'  => 'required|string|max:100|unique:website_settings,profile_key,' . $setting->id,
            'company_name' => 'required|string|max:255',
            'website_url'  => 'nullable|url',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:100',
            'currency_code' => 'nullable|string|max:10',
            'shipping_charge' => 'nullable|numeric',
            'free_shipping_amount' => 'nullable|numeric',
            'address'      => 'nullable|string',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'favicon'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'merchant_qr'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'facebook'     => 'nullable|url',
            'instagram'    => 'nullable|url',
            'youtube'      => 'nullable|url',
            'tiktok'       => 'nullable|url',
            'display_video' => 'nullable|mimes:mp4,mov,ogg,qt|max:30000',
            'slider_image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'slider_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'slider_image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'popup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // dd($data);

        // try {
            if ($req->hasFile('logo')) {
                if ($setting->logo && file_exists(base_path('../' . $setting->logo))) {
                    @unlink(base_path('../' . $setting->logo));
                }
                $data['logo'] = uploadImage($req->file('logo'), 'settings');
            }

            if ($req->hasFile('favicon')) {
                if ($setting->favicon && file_exists(base_path('../' . $setting->favicon))) {
                    @unlink(base_path('../' . $setting->favicon));
                }
                $data['favicon'] = uploadImage($req->file('favicon'), 'settings');
            }

            if ($req->hasFile('merchant_qr')) {
                if ($setting->merchant_qr && file_exists(base_path('../' . $setting->merchant_qr))) {
                    @unlink(base_path('../' . $setting->merchant_qr));
                }
                $data['merchant_qr'] = uploadImage($req->file('merchant_qr'), 'settings');
            }

            if ($req->hasFile('display_video')) {
                if ($setting->display_video && file_exists(base_path('../' . $setting->display_video))) {
                    @unlink(base_path('../' . $setting->display_video));
                }
                $data['display_video'] = uploadFile($req->file('display_video'), 'settings');
            }

            if ($req->hasFile('slider_image1')) {
                if ($setting->slider_image1 && file_exists(base_path('../' . $setting->slider_image1))) {
                    @unlink(base_path('../' . $setting->slider_image1));
                }
                $data['slider_image1'] = uploadImage($req->file('slider_image1'), 'settings');
            }
            if ($req->hasFile('slider_image2')) {
                if ($setting->slider_image2 && file_exists(base_path('../' . $setting->slider_image2))) {
                    @unlink(base_path('../' . $setting->slider_image2));
                }
                $data['slider_image2'] = uploadImage($req->file('slider_image2'), 'settings');
            }
            if ($req->hasFile('slider_image3')) {
                if ($setting->slider_image3 && file_exists(base_path('../' . $setting->slider_image3))) {
                    @unlink(base_path('../' . $setting->slider_image3));
                }
                $data['slider_image3'] = uploadImage($req->file('slider_image3'), 'settings');
            }
            if ($req->hasFile('popup_image')) {
                if ($setting->popup_image && file_exists(base_path('../' . $setting->popup_image))) {
                    @unlink(base_path('../' . $setting->popup_image));
                }
                $data['popup_image'] = uploadImage($req->file('popup_image'), 'settings');
            }

            $data['updated_by'] = auth()->id();

            $setting->update($data);

            return redirect()->route('website-setting.website-settings.index')->with('success', 'Website settings updated successfully');
        // } catch (\Exception $e) {
            // return back()->with('error', 'Failed to update settings: ' . $e->getMessage())->withInput();
        // }
    }

    public function destroy(WebsiteSetting $setting)
    {
        try {
            if ($setting->logo && file_exists(base_path('../' . $setting->logo))) {
                @unlink(base_path('../' . $setting->logo));
            }
            if ($setting->favicon && file_exists(base_path('../' . $setting->favicon))) {
                @unlink(base_path('../' . $setting->favicon));
            }
            if ($setting->merchant_qr && file_exists(base_path('../' . $setting->merchant_qr))) {
                @unlink(base_path('../' . $setting->merchant_qr));
            }
            if ($setting->display_video && file_exists(base_path('../' . $setting->display_video))) {
                @unlink(base_path('../' . $setting->display_video));
            }
            if ($setting->slider_image1 && file_exists(base_path('../' . $setting->slider_image1))) {
                @unlink(base_path('../' . $setting->slider_image1));
            }
            if ($setting->slider_image2 && file_exists(base_path('../' . $setting->slider_image2))) {
                @unlink(base_path('../' . $setting->slider_image2));
            }
            if ($setting->slider_image3 && file_exists(base_path('../' . $setting->slider_image3))) {
                @unlink(base_path('../' . $setting->slider_image3));
            }
            if ($setting->popup_image && file_exists(base_path('../' . $setting->popup_image))) {
                @unlink(base_path('../' . $setting->popup_image));
            }
            $setting->delete();
            return response()->json(['ok' => true, 'msg' => 'Settings deleted']);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'msg' => 'Failed to delete: ' . $e->getMessage()], 500);
        }
    }
}
