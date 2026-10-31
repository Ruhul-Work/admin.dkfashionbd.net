<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
     public function index()
    {
        return view('backend.modules.brands.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'slug','image', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Brand::query()->select(['id', 'name', 'slug','image', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('slug', 'like', "%{$searchVal}%");
                    
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
                <a href="'.route('brand.brands.edit', $b->id).'" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main "
                    data-ajax-modal="' . route('brand.brands.edit', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('brand.brands.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $brand_image = '<div  style="width:70px"><img src="'.image($b->image).'" alt="img"></div>';

            $data[] = [
                $b->id,
                $nameCol,
                $slug = $b->slug,
                $brand_image,
    
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
        return view('backend.modules.brands.create'); // partial only
    }

    public function store(Request $req)
    {
        // dd($req->all());
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150', 'unique:brands,name'],
            'slug'      => ['required', 'string', 'max:50', 'unique:brands,slug'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_title' =>['nullable','string', 'max:150'],
            'meta_description' =>['nullable','string', 'max:150'],
            'meta_keywords' =>['nullable','string', 'max:150'],
            'is_active' =>['required','integer'],
      
           
        ]);

         $imagePath =uploadImage($req->file('image'), 'brand/images');
         $metaImagePath = null;
        if ($req->hasFile('meta_image')) {
            $metaImagePath =uploadImage($req->file('meta_image'), 'brand/meta_images');
        }

         $brand = Brand::create([
             'name'      => ucwords($data['name']),
            'slug'      => ($data['slug']),
            'image'  =>$imagePath,
            'meta_image' => $metaImagePath,
            'meta_title' => $data['meta_title'],
              'meta_description' => $data['meta_description'],
               'meta_keywords' => $data['meta_keywords'],
            'is_active'     => $data['is_active'] ?? null,

        ]);

        return response()->json(['ok' => true, 'msg' => 'Brand created', 'id' => $brand->id]);
     
    }

    public function editModal(Brand $brand)
    {
        
   
        return view('backend.modules.brands.edit', compact('brand'));
    }

    public function show(Brand $brand)
    {

        return response()->json([
            'id'        => $brand->id,
            'name'      => $brand->name,
            'slug'      => $brand->slug,

        ]);
    }

public function update(Request $req, Brand $brand)
{
  
    $data = $req->validate([
        'name'      => [ 'string', 'max:150'],
        'slug'      => [ 'string', 'max:50'],
        'meta_title' => ['nullable','string','max:150'],
        'meta_description' => ['nullable','string','max:150'],
        'meta_keywords' => ['nullable','string','max:150'],
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'meta_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'is_active' => ['required','integer'],
    ]);

    

    $previousImage = $brand->image;
    $previousMetaImage = $brand->meta_image;

   

    if ($req->hasFile('image')) {
        $imagePath = uploadImage($req->file('image'), 'brand/images');
        $brand->image = $imagePath;

        if ($previousImage && file_exists($previousImage)) {
            unlink($previousImage);
        }
    }

    if ($req->hasFile('meta_image')) {
        $metaImagePath = uploadImage($req->file('meta_image'), 'brand/meta_images');
        $brand->meta_image = $metaImagePath;

        if ($previousMetaImage && file_exists($previousMetaImage)) {
            unlink($previousMetaImage);
        }
    }

    $brand->name = ucwords($data['name']);
    $brand->slug = $data['slug'];
    $brand->meta_title = $data['meta_title'] ?? null;
    $brand->meta_description = $data['meta_description'] ?? null;
    $brand->meta_keywords = $data['meta_keywords'] ?? null;
    $brand->is_active = $data['is_active'];

    $brand->save();

      return redirect()->route('brand.brands.index')
                     ->with('success', 'Brand updated successfully!');
}


    public function destroy(Brand $brand)
    {
        $inUse = DB::table('products')->where('brand_id', $brand->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This Brand has {$inUse} product(s). Reassign them first.",
            ], 422);
        }


        $imagePath = $brand->image;
        $metaImagePath = $brand->meta_image;


        $brand->delete();

      
        if (isset($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
        if (isset($metaImagePath) && file_exists($metaImagePath)) {
            unlink($metaImagePath);
        }

        return response()->json(['ok' => true, 'msg' => 'Brand deleted']);
    }

     public function select2(Request $r)
    {

        $q = trim($r->input('q', ''));
        $base = Brand::query()->where('is_active', 1);


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
