<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Category;
use App\Models\backend\CategoryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        return view('backend.modules.categories.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'slug', 'category_type_id', 'icon', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Category::query()
            ->with('category_type:id,name,code')
            ->select(['id', 'name', 'slug', 'category_type_id', 'icon', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('slug', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

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
                    data-ajax-modal="' . route('category.categories.editModal', $b->id) . '"
                    data-size="lg"
                    data-onload="CategoryIndex.onLoad"
                    data-onsuccess="CategoryIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-branch-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('category.categories.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $icon = '<div  style="width:70px"><img src="' . image($b->icon) . '" alt="img"></div>';

            $typeName = $b->category_type->name ?? '—';

            $data[] = [
                $b->id,
                $nameCol,
                e($b->slug),
                // e($typeName),
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
        $types = CategoryType::select('id', 'name')->where('is_active', 1)->get();
        return view('backend.modules.categories.create_modal', compact('types')); // partial only
    }

    public function store(Request $req)
    {
        // dd($req->all());
        $data = $req->validate([
            'name'             => ['required', 'string', 'max:150', 'unique:categories,name'],
            'slug'             => ['required', 'string', 'max:50', 'unique:categories,slug'],
            // 'category_type_id' => ['required', 'integer'],
            'icon'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_title'       => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:150'],
            'meta_keywords'    => ['nullable', 'string', 'max:150'],
            'is_active'        => ['required', 'integer'],

        ]);

        $iconPath      = uploadImage($req->file('icon'), 'category/icons');
        $metaImagePath = null;
        if ($req->hasFile('meta_image')) {
            $metaImagePath = uploadImage($req->file('meta_image'), 'category/meta_images');
        }

        $category = Category::create([
            'name'             => ucwords($data['name']),
            'slug'             => $data['slug'],
            // 'category_type_id' => $data['category_type_id'],
            'icon'             => $iconPath,
            'meta_image'       => $metaImagePath,
            'meta_title'       => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keywords'    => $data['meta_keywords'],
            'is_active'        => $data['is_active'] ?? null,

        ]);

        return response()->json(['ok' => true, 'msg' => 'Category created', 'id' => $category->id]);
    }

    public function editModal(Category $category)
    {

        $types = CategoryType::select('id', 'name')->where('is_active', 1)->get();
        return view('backend.modules.categories.edit_modal', compact('category', 'types'));
    }

    public function show(Category $category)
    {

        return response()->json([
            'id'   => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,

        ]);
    }

    // public function update(Request $req, Category $category)
    // {
    //       $data = $req->validate([
    //         'name'      => ['required', 'string', 'max:150'],
    //         'slug'      => ['required', 'string', 'max:50'],
    //         'meta_title' =>['string', 'max:150'],
    //         'meta_description' =>[ 'string', 'max:150'],
    //         'meta_keywords' =>['string', 'max:150'],
    //         'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //         'meta_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //         'is_active' =>['required','integer'],

    //     ]);

    //          $category = Category::findOrFail($req->id);

    //         $previousIcon = $category->icon;
    //     $previousMetaImage = $category->meta_image;

    //     if ($req->hasFile('icon')) {
    //         $iconPath =uploadImage($req->file('icon'), 'category/icons');
    //         $category->icon = $iconPath;

    //         if ($previousIcon) {
    //             unlink($previousIcon);
    //         }
    //     }

    //      // Upload meta image if provided, otherwise retain the previous value
    //     if ($req->hasFile('meta_image')) {
    //         $metaImagePath =uploadImage($req->file('meta_image'), 'category/meta_images');
    //         $category->meta_image = $metaImagePath;

    //         if ($previousMetaImage) {
    //             unlink($previousMetaImage);
    //         }

    //     }

    //     $category->name      = $data['name'];
    //     $category->slug      = $data['slug'];
    //     $category->meta_title = $data['meta_title'];
    //     $category->meta_description = $data['meta_description'];
    //     $category->meta_keywords = $data['meta_keywords'];
    //     $category->is_active = $data['is_active'];

    //     $category->save();

    //     return response()->json(['ok' => true, 'msg' => 'Category updated']);
    // }
    public function update(Request $req, Category $category)
    {
        $data = $req->validate([
            'name'             => ['required', 'string', 'max:150'],
            'slug'             => ['required', 'string', 'max:50'],
            // 'category_type_id' => ['required', 'integer'],
            'meta_title'       => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:150'],
            'meta_keywords'    => ['nullable', 'string', 'max:150'],
            'icon'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active'        => ['required', 'integer'],
        ]);

        $previousIcon      = $category->icon;
        $previousMetaImage = $category->meta_image;

        if ($req->hasFile('icon')) {
            $iconPath       = uploadImage($req->file('icon'), 'category/icons');
            $category->icon = $iconPath;

            if ($previousIcon && file_exists($previousIcon)) {
                unlink($previousIcon);
            }
        }

        if ($req->hasFile('meta_image')) {
            $metaImagePath        = uploadImage($req->file('meta_image'), 'category/meta_images');
            $category->meta_image = $metaImagePath;

            if ($previousMetaImage && file_exists($previousMetaImage)) {
                unlink($previousMetaImage);
            }
        }

        $category->name             = ucwords($data['name']);
        $category->slug             = $data['slug'];
        // $category->category_type_id = $data['category_type_id'];
        $category->meta_title       = $data['meta_title'] ?? null;
        $category->meta_description = $data['meta_description'] ?? null;
        $category->meta_keywords    = $data['meta_keywords'] ?? null;
        $category->is_active        = $data['is_active'];

        $category->save();

        return response()->json(['ok' => true, 'msg' => 'Category updated']);
    }

    public function destroy(Category $category)
    {
        $inUse = DB::table('subcategories')->where('category_id', $category->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "This category has {$inUse} subcategorie(s). Reassign them first.",
            ], 422);
        }

        $iconPath      = $category->icon;
        $metaImagePath = $category->meta_image;

        $category->delete();

        if (isset($iconPath) && file_exists($iconPath)) {
            unlink($iconPath);
        }
        if (isset($metaImagePath) && file_exists($metaImagePath)) {
            unlink($metaImagePath);
        }

        return response()->json(['ok' => true, 'msg' => 'Category deleted']);
    }

    public function select2(Request $r)
    {
        
        $q = trim($r->input('q', ''));
        // $type = $r->type;
        $base = Category::query()->where('is_active', 1);
      

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
