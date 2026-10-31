<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Category;
use App\Models\backend\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
   
     public function index()
    {
        return view('backend.modules.subcategories.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'slug','category_id','icon', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = SubCategory::query()->select(['id', 'name', 'slug','category_id','icon', 'is_active']);

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
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main AjaxModal"
                    data-ajax-modal="' . route('subcategory.subcategories.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('subcategory.subcategories.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

                $icon = '<div  style="width:70px"><img src="'.image($b->icon).'" alt="img"></div>';

            $data[] = [
                $b->id,
                $nameCol,
                $slug = $b->slug,
                $category = $b->category->name,
                $icon,
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
        $categories = Category::select('id','name')->where('is_active',1)->get();
                                                              // @perm গার্ড চাইলে দিন
        return view('backend.modules.subcategories.create_modal',compact('categories')); // partial only
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150', 'unique:subcategories,name'],
            'slug'      => ['required', 'string', 'max:50', 'unique:subcategories,slug'],
            'category_id' =>['required','integer'],
           'icon' => 'image|mimes:jpeg,png,jpg|max:2048',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_title' =>['nullable', 'string', 'max:150'],
            'meta_description' =>[ 'nullable','string', 'max:150'],
            'meta_keywords' =>[  'nullable','string', 'max:150'],
            'is_active' =>['required','integer'],
      
           
        ]);

              $iconPath =uploadImage($req->file('icon'), 'subcategory/icons');
         $metaImagePath = null;
        if ($req->hasFile('meta_image')) {
            $metaImagePath =uploadImage($req->file('meta_image'), 'subcategory/meta_images');
        }

         $subcategory = SubCategory::create([
             'name'      => ucwords($data['name']),
            'slug'      =>  $data['slug'],
            'category_id' => $data['category_id'],
            'icon'  =>$iconPath,
            'meta_image' => $metaImagePath,
            'meta_title' => $data['meta_title'],
              'meta_description' => $data['meta_description'],
               'meta_keywords' => $data['meta_keywords'],
            'is_active'     => $data['is_active'] ?? null,

        ]);

        return response()->json(['ok' => true, 'msg' => 'Subcategory created', 'id' => $subcategory->id]);
    }

    public function editModal(SubCategory $subcategory)
    {
         $categories = Category::select('id','name')->where('is_active', 1)->get();
        $current_category = Category::where('id',$subcategory->category_id)->first();
   
        return view('backend.modules.subcategories.edit_modal', compact('subcategory','current_category','categories'));
    }

    public function show(SubCategory $subcategory)
    {

        return response()->json([
            'id'        => $subcategory->id,
            'name'      => $subcategory->name,
            'slug'      => $subcategory->slug,
            'category' => $subcategory->category->name

        ]);
    }

    public function update(Request $req, SubCategory $subcategory)
    {
          $data = $req->validate([
            'name'      => [ 'string', 'max:150'],
            'slug'      => ['string', 'max:50'],
            'category_id' => ['required', 'integer'],
            'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_title' =>['nullable','string', 'max:150'],
            'meta_description' =>['nullable','string', 'max:150'],
            'meta_keywords' =>['nullable','string', 'max:150'],
            'is_active' =>['required','integer'],

      
           
        ]);

            // $subcategory = Subcategory::findOrFail($req->id);

    $previousIcon = $subcategory->icon;
    $previousMetaImage = $subcategory->meta_image;

    if ($req->hasFile('icon')) {
        $iconPath = uploadImage($req->file('icon'), 'subcategory/icons');
        $subcategory->icon = $iconPath;

        if ($previousIcon && file_exists($previousIcon)) {
            unlink($previousIcon);
        }
    }

    if ($req->hasFile('meta_image')) {
        $metaImagePath = uploadImage($req->file('meta_image'), 'subcategory/meta_images');
        $subcategory->meta_image = $metaImagePath;

        if ($previousMetaImage && file_exists($previousMetaImage)) {
            unlink($previousMetaImage);
        }
    }


        $subcategory->name      = ucwords($data['name']);
        $subcategory->slug      = $data['slug'];
        $subcategory->category_id = $data['category_id'];
         $subcategory->meta_title = $data['meta_title'];
        $subcategory->meta_description = $data['meta_description'];
        $subcategory->meta_keywords = $data['meta_keywords'];
        $subcategory->is_active = $data['is_active'];

        $subcategory->save();

        return response()->json(['ok' => true, 'msg' => 'subcategory updated']);
    }

    public function destroy(Subcategory $subcategory)
    {
        $inUse = DB::table('products')->where('subcategory_id', $subcategory->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This Subcategory has {$inUse} product(s). Reassign them first.",
            ], 422);
        }

        // DB::table('branch_business')->where('branch_id', $district->district_id)->delete();
        
        $iconPath = $subcategory->icon;
        $metaImagePath = $subcategory->meta_image;

        $subcategory->delete();

         
        if (isset($iconPath) && file_exists($iconPath)) {
            unlink($iconPath);
        }
        if (isset($metaImagePath) && file_exists($metaImagePath)) {
            unlink($metaImagePath);
        }


        return response()->json(['ok' => true, 'msg' => 'subcategory deleted']);
    }

     public function select2(Request $r)
    {
        
        $q = trim($r->input('q', ''));
        $type = $r->type;
        $base = SubCategory::query()->where('category_id',$type)->where('is_active', 1);
      

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
