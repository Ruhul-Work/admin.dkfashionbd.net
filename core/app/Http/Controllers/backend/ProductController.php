<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Product;
use App\Models\backend\ProductSpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Jobs\SyncProductToSecondApp;

class ProductController extends Controller
{
    public function index()
    {
        return view('backend.modules.products.index');
    }

    // public function productList()
    // {
    //     $products = Product::with(['category'])
    //         ->where('parent_id', null)
    //         ->paginate(100);

    //     // $html =  view('backend.modules.products.product_list', compact('products'))->render();

    //     return response()->json(['products' => $products]);
    // }

    // public function productList()
    // {
    //     try {
    //         $branchId = current_branch_id();
    //         $products = Product::query()
    //             ->with(['category:id,name'])
    //             ->whereNull('products.parent_id')
    //             ->select(
    //                 'products.id',
    //                 'products.name',
    //                 'products.price',
    //                 'products.cost_price',
    //                 'products.mrp',
    //                 'products.image',
    //                 'products.category_id',
    //                 \DB::raw("(SELECT SUM(sc.quantity) FROM stock_currents sc
    //                            LEFT JOIN products p2 ON sc.product_id = p2.id
    //                            WHERE sc.branch_id = $branchId
    //                            AND (sc.product_id = products.id OR p2.parent_id = products.id)) as stock")
    //             )
    //             ->paginate(100);

    //         return response()->json(['products' => $products], 200);
    //     } catch (\Throwable $e) {
    //         \Log::error("PRODUCT LIST ERROR", [
    //             'error' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'message' => 'Server error loading products',
    //         ], 500);
    //     }
    // }
    public function productList()
    {
        try {

            $branchId = current_branch_id();

            $products = Product::query()
                ->leftJoin('products as child', 'child.parent_id', '=', 'products.id')
                ->leftJoin('stock_currents as sc', function ($join) use ($branchId) {

                    $join->on(function ($q) {
                        $q->on('sc.product_id', '=', 'products.id')
                            ->orOn('sc.product_id', '=', 'child.id');
                    });

                    if ($branchId) {
                        $join->where('sc.branch_id', $branchId);
                    }
                })
                ->with(['category:id,name'])
                ->whereNull('products.parent_id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.has_variants',
                    'products.price',
                    'products.cost_price',
                    'products.mrp',
                    'products.thumbnail_image',
                    'products.category_id',
                    \DB::raw('COALESCE(SUM(sc.quantity),0) as stock')
                )
                ->groupBy(
                    'products.id',
                    'products.name',
                    'products.has_variants',
                    'products.price',
                    'products.cost_price',
                    'products.mrp',
                    'products.thumbnail_image',
                    'products.category_id'
                )
                ->paginate(100);

            return response()->json([
                'products' => $products,
            ], 200);
        } catch (\Throwable $e) {

            \Log::error("PRODUCT LIST ERROR", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Server error loading products',
            ], 500);
        }
    }

    public function productSearch($name)
    {
        $branchId = current_branch_id();

        $products = Product::query()
            ->leftJoin('products as child', 'child.parent_id', '=', 'products.id')
            ->leftJoin('stock_currents as sc', function ($join) use ($branchId) {

                $join->on('sc.product_id', '=', 'child.id');

                if ($branchId) {
                    $join->where('sc.branch_id', $branchId);
                }
            })
            ->with(['category'])
            ->whereNull('products.parent_id')
            ->where('products.name', 'like', '%' . $name . '%')
            ->select(
                'products.id',
                'products.name',
                'products.has_variants',
                'products.price',
                'products.cost_price',
                'products.mrp',
                'products.thumbnail_image',
                'products.category_id',
                \DB::raw('COALESCE(SUM(sc.quantity),0) as stock')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.has_variants',
                'products.price',
                'products.cost_price',
                'products.mrp',
                'products.thumbnail_image',
                'products.category_id'
            )
            ->paginate(100);

        return response()->json(['products' => $products]);
    }

    public function productByCategory($category)
    {
        $branchId = current_branch_id();

        $products = Product::query()
            ->leftJoin('products as child', 'child.parent_id', '=', 'products.id')
            ->leftJoin('stock_currents as sc', function ($join) use ($branchId) {

                $join->on('sc.product_id', '=', 'child.id');

                if ($branchId) {
                    $join->where('sc.branch_id', $branchId);
                }
            })
            ->with(['category'])
            ->whereNull('products.parent_id')
            ->where('products.category_id', $category)
            ->select(
                'products.id',
                'products.name',
                'products.has_variants',
                'products.price',
                'products.cost_price',
                'products.mrp',
                'products.thumbnail_image',
                'products.category_id',
                \DB::raw('COALESCE(SUM(sc.quantity),0) as stock')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.has_variants',
                'products.price',
                'products.cost_price',
                'products.mrp',
                'products.thumbnail_image',
                'products.category_id'
            )
            ->paginate(100);

        return response()->json(['products' => $products]);
    }

    public function childProductList(Product $product)
    {
        $branchId = current_branch_id();

        $query = Product::query()
            ->leftJoin('stock_currents', function ($join) use ($branchId) {

                $join->on('products.id', '=', 'stock_currents.product_id');

                if ($branchId) {
                    $join->where('stock_currents.branch_id', $branchId);
                }
            })
            ->select(
                'products.id',
                'products.parent_id',
                'products.name',
                'products.price',
                'products.cost_price',
                'products.mrp',
                \DB::raw('COALESCE(SUM(stock_currents.quantity),0) as stock')
            );

        if ($product->has_variants == 1) {

            $query->where('products.parent_id', $product->id);
        } else {

            $query->where('products.id', $product->id);
        }

        $products = $query
            ->groupBy(
                'products.id',
                'products.parent_id',
                'products.name',
                'products.price',
                'products.cost_price',
                'products.mrp'
            )
            ->get();

        return response()->json(['products' => $products]);
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'price', 'is_active', 'has_variants', 'name', 'category_id', 'subcategory_id', 'brand_id', 'thumbnail_image'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Product::query()->select(['id', 'price', 'is_active', 'has_variants', 'name', 'category_id', 'subcategory_id', 'brand_id', 'thumbnail_image'])->where('parent_id', null);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('slug', 'like', "%{$searchVal}%")
                    ->orWhere('sku', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<p>' . e($b->name) .
                ($b->has_variants === 1 ? ' <a href="' . route('product.products.show', $b->id) . '" class="badge bg-success">Has Child</a>' : '') .
                '</p>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="' . route('product.products.editModal', $b->id) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                    bg-success-focus text-success-main "
                    data-ajax-modal="' . route('product.products.editModal', $b->id) . '"
                    data-size="lg"
                    data-onsuccess="BranchesIndex.onSaved"
                    title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-product-delete"
                    data-id="' . $b->id . '"
                    data-url="' . route('product.products.destroy', $b->id) . '"
                    title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';
            $image  = '<div  style="width:70px"><img src="' . image($b->thumbnail_image) . '" alt="img"></div>';
            $status = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';
            // $size  = ($b->size && $b->size->name) ? $b->size->name : '';
            // $color = ($b->color && $b->color->name) ? $b->color->name : '';

            // $size_color = '<p>' . $size . '<br><span class="text-sm">' . $color . '</span></p>';
            $category   = ' <span class="text-sm">Category: ' . ($b->category?->name ?? 'N/A') . '</span><br>
                        <span class="text-sm">Sub-Category: ' . ($b->subcategory?->name ?? 'N/A') . '</span>';

            $data[] = [
                $b->id,
                $nameCol,
                $category,
                // $b->product_type->name,
                $b->brand?->name ?? 'N/A',
                $b->price,
                // $size_color,
                $image,
                $status,
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
        return view('backend.modules.products.create'); // partial only
    }

    public function store(Request $req)
    {

        $data = $req->validate(
            [
                'name'              => ['required', 'string', 'max:150', 'unique:products,name'],
                'slug'              => ['required', 'string', 'max:150', 'unique:products,slug'],
                'sku'               => ['required', 'string', 'max:150', 'unique:products,sku'],
                // 'category_type_id'  => ['required', 'integer'],
                'category_id'       => ['required', 'integer'],
                'subcategory_id'    => ['nullable', 'integer'],
                // 'product_type_id'   => ['required', 'integer'],
                'brand_id'          => ['required', 'integer'],
                'color_id.*'        => 'nullable|integer',
                'unit_id'           => ['nullable', 'integer'],
                'size_id.*'         => ['nullable', 'integer'],
                'paper_id.*'        => ['nullable', 'integer'],
                'cost_price'        => ['required', 'numeric'],
                'mrp'               => ['required', 'numeric'],
                'discount_type'     => ['nullable', 'in:percent,flat'],
                'discount_value'    => ['required', 'numeric', 'min:0'],
                'price'             => ['required', 'numeric'],
                'has_variant'       => ['required', 'integer', 'in:0,1'],
                'is_active'         => ['required', 'integer'],
                'material'          => ['nullable', 'string', 'max:150'],
                // 'weight'            => ['nullable', 'numeric'],
                'fabric_weave'      => ['nullable', 'string', 'max:150'],
                'fabric_type'       => ['nullable', 'string', 'max:150'],
                'weight_gsm'        => ['nullable', 'numeric'],
                'origin_country'    => ['nullable', 'string', 'max:150'],
                'care_instructions' => ['nullable', 'string', 'max:250'],
                'durability_rating' => ['nullable', 'string', 'max:150'],
                'transparency_text' => ['nullable', 'string', 'max:150'],
                'description'       => ['nullable', 'string', 'max:350'],
                'short_description' => ['nullable', 'string', 'max:250'],
                'images[]'          => 'nullable|array',
                'images.*'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'thumbnail_image'   => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'size_chart_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'meta_title'        => ['nullable', 'string', 'max:150'],
                'meta_description'  => ['nullable', 'string', 'max:350'],
                'meta_keywords'     => ['nullable', 'string', 'max:250'],
                'meta_image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ],
            [
                'image.required'           => 'Product image must be jpeg, png, or jpg and max size 2MB',
                'thumbnail_image.required' => 'Product thumbnail image is must be jpeg, png, or jpg and max size 2MB',
                'size_chart_image.image'   => 'Size chart image must be an image file',
                'meta_image.image'         => 'Meta image must be a jpeg, png, or jpg file and max size 2MB',

            ]
        );

        $child_data = null;

        if ($data['has_variant'] == 1) {
            $child_data = $req->validate([
                'child_name'       => 'required',
                'child_sku'        => 'required',
                'child_color_id.*' => 'nullable|integer',
                'child_size_id.*'  => 'nullable|integer',
                'child_paper_id.*' => 'nullable|integer',
                'child_price.*'    => 'required|numeric',
            ]);
        }

        $discountValue = (float) $data['discount_value'];
        $discountType  = $discountValue > 0 ? $data['discount_type'] : null;

        if ($discountValue > 0 && $discountType === null) {
            return response()->json([
                'message' => 'Select a discount type for a discount value above zero.',
                'errors' => [
                    'discount_type' => ['Select a discount type for a discount value above zero.'],
                ],
            ], 422);
        }

        $final_price = $data['mrp'];

        if ($discountType === 'flat') {
            $final_price -= $discountValue;
        } elseif ($discountType === 'percent') {
            $final_price -= ($data['mrp'] * $discountValue) / 100;
        }

        $imagePaths = [];
        if ($req->hasFile('images')) {
            foreach ($req->file('images') as $image) {
                $imagePaths[] = uploadImage($image, 'product/images');
            }
        }

        $thumbnail_image  = uploadImage($req->file('thumbnail_image'), 'product/thumbnail_images');
        $size_chart_image = null;

        if ($req->hasFile('size_chart_image')) {
            $size_chart_image = uploadImage($req->file('size_chart_image'), 'product/size_chart_images');
        }
        $metaImagePath = null;
        if ($req->hasFile('meta_image')) {
            $metaImagePath = uploadImage($req->file('meta_image'), 'product/meta_images');
        }

        $product = product::create([
            'name'              => ucwords($data['name']),
            'slug'              => $data['slug'],
            'sku'               => $data['sku'],
            'barcode'           => $data['has_variant'] == 0 ? $this->generateUniqueEan13() : null,
            // 'category_type_id'  => $data['category_type_id'],
            'category_id'       => $data['category_id'],
            'subcategory_id'    => $data['subcategory_id'] ?? null,
            // 'product_type_id'   => $data['product_type_id'],
            'brand_id'          => $data['brand_id'],
            'size_id'           => null,
            'color_id'          => null,
            // 'unit_id'           => $data['unit_id'],
            'cost_price'        => $data['cost_price'],
            'mrp'               => $data['mrp'],
            'discount_type'     => $discountType,
            'discount_value'    => $discountValue,
            'price'             => $final_price,
            'has_variants'      => $data['has_variant'] ?? 0,
            'is_sellable'       => ($data['has_variant'] == 0 ? 1 : 0),
            'material'          => $data['material'],
            'description'       => $data['description'],
            'short_description' => $data['short_description'],
            'is_active'         => $data['is_active'],
            'meta_title'        => $data['meta_title'],
            'meta_keywords'     => $data['meta_keywords'],
            'meta_description'  => $data['meta_description'],
            'images'            => json_encode($imagePaths),
            'thumbnail_image'   => $thumbnail_image,
            'size_chart_image'  => $size_chart_image,
            'meta_image'        => $metaImagePath,

        ]);

        ProductSpecification::create([
            'product_id' => $product->id,
            'material'   => $data['material'],
            'weight_gsm' => $data['weight_gsm'],
            'origin_country' => $data['origin_country'],
            'fabric_weave' => $data['fabric_weave'],
            'fabric_type' => $data['fabric_type'],
            'care_instructions' => $data['care_instructions'],
            'durability_rating' => $data['durability_rating'],
            'transparency_text' => $data['transparency_text'],
        ]);

        if ($data['has_variant'] == 1) {
            $colors = $child_data['child_color_id'] ?? [];
            $sizes  = $child_data['child_size_id'] ?? [];
            $papers = $child_data['child_paper_id'] ?? [];
            // $variant = $data['variant_type'];
            $names  = $child_data['child_name'];
            $skus   = $child_data['child_sku'];
            $prices = $child_data['child_price'];

            if (sizeof($colors) > 0 && sizeof($sizes) > 0) {

                for ($i = 0; $i < sizeof($colors); $i++) {
                    $child_product = product::create([
                        'parent_id'        => $product->id,
                        'name'             => ucwords($names[$i]),
                        'slug'             => Str::slug($child_data['child_name'][$i], '-'),
                        'sku'              => $skus[$i],
                        'barcode'          => $this->generateUniqueEan13(),
                        // 'category_type_id' => $data['category_type_id'],
                        'category_id'      => $data['category_id'],
                        'subcategory_id'   => $data['subcategory_id'] ?? null,
                        // 'product_type_id'  => $data['product_type_id'],
                        'brand_id'         => $data['brand_id'],
                        'size_id'          => $child_data['child_size_id'][$i],
                        'color_id'         => $child_data['child_color_id'][$i],
                        // 'unit_id'          => $data['unit_id'],
                        'cost_price'       => $data['cost_price'],
                        'mrp'              => $data['mrp'],
                        'discount_type'    => $discountType,
                        'discount_value'   => $discountValue,
                        'price'            => $prices[$i],
                        'has_variants'     => 0,
                        'is_sellable'      => 1,
                        'is_active'        => $data['is_active'],
                        'images'            => json_encode($imagePaths),
                        'thumbnail_image'  => $thumbnail_image,
                        'size_chart_image' => $size_chart_image,
                        'meta_image'       => $metaImagePath,
                    ]);
                }
            } else {
                if (sizeof($colors) > 0) {

                    for ($i = 0; $i < sizeof($colors); $i++) {
                        $child_product = product::create([
                            'parent_id'        => $product->id,
                            'name'             => ucwords($names[$i]),
                            'slug'             => Str::slug($child_data['child_name'][$i], '-'),
                            'sku'              => $skus[$i],
                            'barcode'          => $this->generateUniqueEan13(),
                            // 'category_type_id' => $data['category_type_id'],
                            'category_id'      => $data['category_id'],
                            'subcategory_id'   => $data['subcategory_id'] ?? null,
                            // 'product_type_id'  => $data['product_type_id'],
                            'brand_id'         => $data['brand_id'],
                            // 'size_id'      => $sizes[$i],
                            'color_id'         => $colors[$i],
                            // 'unit_id'          => $data['unit_id'],
                            'cost_price'       => $data['cost_price'],
                            'mrp'              => $data['mrp'],
                            'discount_type'    => $discountType,
                            'discount_value'   => $discountValue,
                            'price'            => $prices[$i],
                            'has_variants'     => 0,
                            'is_sellable'      => 1,
                            'is_active'        => $data['is_active'],
                            'thumbnail_image'  => $thumbnail_image,
                            'images'           => json_encode($imagePaths),
                        ]);
                    }
                } elseif (sizeof($sizes) > 0) {

                    for ($i = 0; $i < sizeof($sizes); $i++) {
                        $child_product = product::create([
                            'parent_id'        => $product->id,
                            'name'             => ucwords($names[$i]),
                            'slug'             => Str::slug($child_data['child_name'][$i], '-'),
                            'sku'              => $skus[$i],
                            'barcode'          => $this->generateUniqueEan13(),
                            // 'category_type_id' => $data['category_type_id'],
                            'category_id'      => $data['category_id'],
                            'subcategory_id'   => $data['subcategory_id'] ?? null,
                            // 'product_type_id'  => $data['product_type_id'],
                            'brand_id'         => $data['brand_id'],
                            'size_id'          => $sizes[$i],
                            // 'color_id'      => $colors[$i],
                            // 'unit_id'          => $data['unit_id'],
                            'cost_price'       => $data['cost_price'],
                            'mrp'              => $data['mrp'],
                            'discount_type'    => $discountType,
                            'discount_value'   => $discountValue,
                            'price'            => $prices[$i],
                            'has_variants'     => 0,
                            'is_sellable'      => 1,
                            'is_active'        => $data['is_active'],
                            'thumbnail_image'  => $thumbnail_image,
                            'images'            => json_encode($imagePaths),
                        ]);
                    }
                } else {

                    for ($i = 0; $i < sizeof($papers); $i++) {
                        $child_product = product::create([
                            'parent_id'        => $product->id,
                            'name'             => ucwords($names[$i]),
                            'sku'              => $skus[$i],
                            'barcode'          => $this->generateUniqueEan13(),
                            'slug'             => Str::slug($child_data['child_name'][$i], '-'),
                            // 'category_type_id' => $data['category_type_id'],
                            'category_id'      => $data['category_id'],
                            'subcategory_id'   => $data['subcategory_id'] ?? null,
                            // 'product_type_id'  => $data['product_type_id'],
                            'brand_id'         => $data['brand_id'],
                            'paper_id'         => $papers[$i],
                            // 'unit_id'          => $data['unit_id'],
                            'cost_price'       => $data['cost_price'],
                            'mrp'              => $data['mrp'],
                            'discount_type'    => $discountType,
                            'discount_value'   => $discountValue,
                            'price'            => $prices[$i],
                            'has_variants'     => 0,
                            'is_sellable'      => 1,
                            'is_active'        => $data['is_active'],
                            'thumbnail_image'  => $thumbnail_image,
                            'images'            => json_encode($imagePaths),
                        ]);
                    }
                }
            }
        }

        SyncProductToSecondApp::dispatch($product->id);

        return response()->json(['ok' => true, 'message' => 'Product created successfully', 'id' => $product->id]);
    }

    public function editModal(Product $product)
    {
        $colors = Product::where('parent_id', $product->id)
            ->whereNotNull('color_id')
            ->with('color')
            ->get()
            ->unique('color_id'); // filter duplicates in PHP
        $sizes = Product::where('parent_id', $product->id)
            ->whereNotNull('size_id')
            ->with('size')
            ->get()
            ->unique('size_id'); // filter duplicates in PHP
        $papers = Product::where('parent_id', $product->id)
            ->whereNotNull('paper_id')
            ->with('paper_quality')
            ->get()
            ->unique('paper_id'); // filter duplicates in PHP

        $products = Product::with('specifications')->where('parent_id', $product->id)->get();

        $product->load('specifications');
        // dd($product);

        return view('backend.modules.products.edit', compact('product'), ['colors' => $colors, 'sizes' => $sizes, 'papers' => $papers, 'products' => $products]);
    }

    public function show(Product $product)
    {
        $childProducts = Product::where('parent_id', $product->id)->get();

        return view('backend.modules.products.view', ['product' => $product, 'child_products' => $childProducts]);
    }

    public function update(Request $req, Product $product)
    {
        $validator = Validator::make(
            $req->all(),
            [
                'name'              => ['required', 'string', 'max:150'],
                'slug'              => ['required', 'string', 'max:150'],
                'sku'               => ['required', 'string', 'max:150'],
                // 'category_type_id'  => ['required', 'integer'],
                'category_id'       => ['required', 'integer'],
                'subcategory_id'    => ['nullable', 'integer'],
                // 'product_type_id'   => ['required', 'integer'],
                'brand_id'          => ['required', 'integer'],
                // 'unit_id'           => ['required', 'integer'],
                'cost_price'        => ['required', 'numeric'],
                'mrp'               => ['required', 'numeric'],
                'discount_type'     => ['nullable', 'in:percent,flat'],
                'discount_value'    => ['required', 'numeric', 'min:0'],
                'price'             => ['required', 'numeric'],
                'has_variant'       => ['required', 'integer', 'in:0,1'],
                'is_active'         => ['required', 'integer'],
                'material'          => ['nullable', 'string', 'max:150'],
                'weight_gsm'        => ['nullable', 'numeric'],
                'fabric_weave'      => ['nullable', 'string', 'max:150'],
                'fabric_type'       => ['nullable', 'string', 'max:150'],
                'origin_country'    => ['nullable', 'string', 'max:150'],
                'care_instructions'  => ['nullable', 'string', 'max:250'],
                'durability_rating' => ['nullable', 'string', 'max:150'],
                'transparency_text' => ['nullable', 'string', 'max:150'],
                'description'       => ['nullable', 'string', 'max:350'],
                'short_description' => ['nullable', 'string', 'max:250'],
                'image'             => 'nullable|array',
                'image.*'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'thumbnail_image'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'size_chart_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'meta_title'        => ['nullable', 'string', 'max:150'],
                'meta_description'  => ['nullable', 'string', 'max:350'],
                'meta_keywords'     => ['nullable', 'string', 'max:250'],
                'meta_image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ],
            [

                'image.image'            => 'Product image must be an image file',
                'image.mimes'            => 'Product image must be jpeg, png, or jpg',
                'image.max'              => 'Product image size must be less than 2MB',
                'thumbnail_image.image'  => 'Product thumbnail image must be an image file',
                'thumbnail_image.mimes'  => 'Product thumbnail image must be jpeg, png, or jpg',
                'thumbnail_image.max'    => 'Product thumbnail image size must be less than 2MB',
                'size_chart_image.image' => 'Size chart image must be an image file',
                'size_chart_image.mimes' => 'Size chart image must be jpeg, png, or jpg',
                'size_chart_image.max'   => 'Size chart image size must be less than 2MB',
                'meta_image.image'       => 'Meta image must be an image file',
                'meta_image.mimes'       => 'Meta image must be jpeg, png, or jpg',
                'meta_image.max'         => 'Meta image size must be less than 2MB',

            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $requestedHasVariants = (int) $data['has_variant'];

        if ($product->has_variants === 1 && $requestedHasVariants === 0) {
            return redirect()->back()
                ->withErrors(['has_variant' => 'A variant product cannot be converted back to a single product.'])
                ->withInput();
        }

        $childData = null;
        if ($requestedHasVariants === 1) {
            $childValidator = Validator::make($req->all(), [
                'child_id'         => ['nullable', 'array'],
                'child_id.*'       => ['nullable', 'integer'],
                'child_name'       => ['required', 'array', 'min:1'],
                'child_name.*'     => ['required', 'string', 'max:150'],
                'child_sku'        => ['required', 'array', 'min:1'],
                'child_sku.*'      => ['required', 'string', 'max:150', 'distinct'],
                'child_color_id'   => ['nullable', 'array'],
                'child_color_id.*' => ['nullable', 'integer'],
                'child_size_id'    => ['nullable', 'array'],
                'child_size_id.*'  => ['nullable', 'integer'],
                'child_paper_id'   => ['nullable', 'array'],
                'child_paper_id.*' => ['nullable', 'integer'],
                'child_price'      => ['required', 'array', 'min:1'],
                'child_price.*'    => ['required', 'numeric', 'min:0'],
            ], [
                'child_name.required' => 'Select at least one Color or Size to create product variants.',
            ]);

            if ($childValidator->fails()) {
                return redirect()->back()->withErrors($childValidator)->withInput();
            }

            $childData = $childValidator->validated();
        }

        $previousThumbnailImage = $product->thumbnail_image;
        $previousSizeImage      = $product->size_chart_image;
        $previousMetaImage      = $product->meta_image;

        $existing_images = $req->input('existing_images', []);
        $new_images = [];
        if ($req->hasFile('image')) {
            foreach ($req->file('image') as $file) {
                $new_images[] = uploadImage($file, 'product/images');
            }
        }

        $final_images = array_merge($existing_images, $new_images);
        $final_images = array_slice($final_images, 0, 3); // max 3 images

        // Delete removed images from storage
        $old_images = json_decode($product->images, true) ?: [];
        foreach ($old_images as $old) {
            if (!in_array($old, $final_images)) {
                if ($old && file_exists(public_path($old))) {
                    @unlink(public_path($old));
                }
            }
        }

        $product->images = json_encode($final_images);
        // $product->image  = !empty($final_images) ? $final_images[0] : null;

        $metaImagePath = null;
        if ($req->hasFile('meta_image')) {
            $metaImagePath       = uploadImage($req->file('meta_image'), 'product/meta_images');
            $product->meta_image = $metaImagePath;

            if ($previousMetaImage && file_exists($previousMetaImage)) {
                unlink($previousMetaImage);
            }
        } elseif ($req->remove_meta_image == 1) {
            if ($previousMetaImage && file_exists($previousMetaImage)) {
                unlink($previousMetaImage);
            }
            $product->meta_image = null;
        }

        $thumbnail_imagePath = null;
        if ($req->hasFile('thumbnail_image')) {
            $thumbnail_imagePath      = uploadImage($req->file('thumbnail_image'), 'product/thumbnail_images');
            $product->thumbnail_image = $thumbnail_imagePath;

            if ($previousThumbnailImage && file_exists($previousThumbnailImage)) {
                unlink($previousThumbnailImage);
            }
        } elseif ($req->remove_thumbnail_image == 1) {
            if ($previousThumbnailImage && file_exists($previousThumbnailImage)) {
                unlink($previousThumbnailImage);
            }
            $product->thumbnail_image = null;
        }

        $sizeImagePath = null;
        if ($req->hasFile('size_chart_image')) {
            $sizeImagePath             = uploadImage($req->file('size_chart_image'), 'product/size_chart_images');
            $product->size_chart_image = $sizeImagePath;

            if ($previousSizeImage && file_exists($previousSizeImage)) {
                unlink($previousSizeImage);
            }
        } elseif ($req->remove_size_chart_image == 1) {
            if ($previousSizeImage && file_exists($previousSizeImage)) {
                unlink($previousSizeImage);
            }
            $product->size_chart_image = null;
        }

        $variantImagePath     = json_decode($product->images);
        $variantThumbnailPath = $product->thumbnail_image;
        $variantSizePath      = $product->size_chart_image;
        $variantMetaPath      = $product->meta_image;

        $discountValue = (float) $data['discount_value'];
        $discountType  = $discountValue > 0 ? $data['discount_type'] : null;

        if ($discountValue > 0 && $discountType === null) {
            return redirect()->back()
                ->withErrors(['discount_type' => 'Select a discount type for a discount value above zero.'])
                ->withInput();
        }

        $final_price = $data['mrp'];

        if ($discountType === 'flat') {
            $final_price -= $discountValue;
        } elseif ($discountType === 'percent') {
            $final_price -= ($data['mrp'] * $discountValue) / 100;
        }

        $product->name             = ucwords($data['name']);
        $product->slug             = $data['slug'];
        $product->sku              = $data['sku'];
        $product->barcode          = $requestedHasVariants === 0
            ? $this->existingOrNewEan13($product->barcode)
            : null;
        // $product->category_type_id = $data['category_type_id'];
        $product->category_id      = $data['category_id'];
        $product->subcategory_id   = $data['subcategory_id'] ?? null;
        // $product->size_id = $data['size_id'];
        // $product->color_id = $data['color_id'];
        $product->brand_id          = $data['brand_id'];
        // $product->unit_id           = $data['unit_id'];
        // $product->product_type_id   = $data['product_type_id'];
        $product->cost_price        = $data['cost_price'];
        $product->mrp               = $data['mrp'];
        $product->discount_type     = $discountType;
        $product->discount_value    = $discountValue;
        $product->price             = $final_price;
        $product->material          = $data['material'];
        $product->description       = $data['description'];
        $product->short_description = $data['short_description'];
        $product->is_active         = $data['is_active'];
        $product->has_variants      = $requestedHasVariants;
        $product->is_sellable       = $requestedHasVariants === 1 ? 0 : 1;
        $product->meta_description  = $data['meta_description'];
        $product->meta_title        = $data['meta_title'];
        $product->meta_keywords     = $data['meta_keywords'];

        $product->save();

        // Update or create product specifications
        ProductSpecification::updateOrCreate(
            ['product_id' => $product->id],
            [
                'material'          => $data['material'],
                'weight_gsm'        => $data['weight_gsm'],
                'fabric_weave'      => $data['fabric_weave'],
                'fabric_type'       => $data['fabric_type'],
                'origin_country'    => $data['origin_country'],
                'care_instructions'  => $data['care_instructions'],
                'durability_rating' => $data['durability_rating'],
                'transparency_text' => $data['transparency_text'],
            ]
        );

        if ($requestedHasVariants === 1) {
            $submittedChildIds = array_filter(array_map('intval', $childData['child_id'] ?? []));
            Product::where('parent_id', $product->id)
                ->whereNotIn('id', $submittedChildIds)
                ->delete();

            for ($i = 0; $i < count($childData['child_name']); $i++) {
                $id = ! empty($childData['child_id'][$i]) ? (int) $childData['child_id'][$i] : null;
                $child_product = $id
                    ? Product::where('parent_id', $product->id)->where('id', $id)->first()
                    : null;

                if ($child_product) {
                    $child_product->name             = $childData['child_name'][$i];
                    $child_product->sku              = $childData['child_sku'][$i];
                    $child_product->barcode          = $this->existingOrNewEan13($child_product->barcode);
                    $child_product->color_id         = $childData['child_color_id'][$i] ?? null;
                    $child_product->size_id          = $childData['child_size_id'][$i] ?? null;
                    $child_product->paper_id         = $childData['child_paper_id'][$i] ?? null;
                    $child_product->price            = $childData['child_price'][$i];
                    $child_product->images           = json_encode($variantImagePath);
                    $child_product->thumbnail_image  = $variantThumbnailPath;
                    $child_product->size_chart_image = $variantSizePath;
                    $child_product->meta_image       = $variantMetaPath;

                    $child_product->save();
                } else {
                    $child_product = Product::create([
                        'parent_id'        => $product->id,
                        'name'             => ucwords($childData['child_name'][$i]),
                        'slug'             => Str::slug($childData['child_name'][$i], '-'),
                        'sku'              => $childData['child_sku'][$i],
                        'barcode'          => $this->generateUniqueEan13(),
                        // 'category_type_id' => $data['category_type_id'],
                        'category_id'      => $data['category_id'],
                        'subcategory_id'   => $data['subcategory_id'] ?? null,
                        // 'product_type_id'  => $data['product_type_id'],
                        'brand_id'         => $data['brand_id'],
                        'size_id'          => $childData['child_size_id'][$i] ?? null,
                        'color_id'         => $childData['child_color_id'][$i] ?? null,
                        'paper_id'         => $childData['child_paper_id'][$i] ?? null,
                        // 'unit_id'          => $data['unit_id'],
                        'cost_price'       => $data['cost_price'],
                        'mrp'              => $data['mrp'],
                        'discount_type'    => $discountType,
                        'discount_value'   => $discountValue,
                        'price'            => $childData['child_price'][$i],
                        'has_variants'     => 0,
                        'is_sellable'      => 1,
                        'is_active'        => $data['is_active'],
                        'images'            => json_encode($variantImagePath),
                        'thumbnail_image'  => $variantThumbnailPath,
                        'size_chart_image' => $variantSizePath,
                        'meta_image'       => $variantMetaPath,
                    ]);
                }
            }
        }

        SyncProductToSecondApp::dispatch($product->id);

        return redirect()
            ->route('product.products.index')
            ->with('success', 'Product updated successfully');
    }
    // public function importCsvModal()
    // {
    //     return view('backend.modules.products.import_csv');
    // }

    // public function importCsv(Request $req)
    // {

    //     $sheets[] = $req->all();
    //     if (empty($sheets) || empty($sheets[0])) {
    //         return back()->with('error', 'Uploaded file is empty or unreadable.');
    //     }

    //     $rows = $sheets[0];

    //     // dd($rows);

    //     // If first row is header, normalize it and map rows to assoc arrays
    //     $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
    //     $dataRows = array_slice($rows, 1);

    //     $allowed = [
    //         'parent_id',
    //         'has_variants',
    //         'is_sellable',
    //         'name',
    //         'slug',
    //         'sku',
    //         'barcode',
    //         'unit_id',
    //         'brand_id',
    //         'product_type_id',
    //         'category_type_id',
    //         'category_id',
    //         'subcategory_id',
    //         'tax_id',
    //         'tax_included',
    //         'color_id',
    //         'size_id',
    //         'paper_id',
    //         'price',
    //         'cost_price',
    //         'mrp',
    //         'discount_type',
    //         'discount_value',
    //         'discount_starts_at',
    //         'discount_ends_at',
    //         'track_stock',
    //         'reorder_level',
    //         'image',
    //         'thumbnail_image',
    //         'size_chart_image',
    //         'material',
    //         'meta_title',
    //         'meta_description',
    //         'meta_keywords',
    //         'meta_image',
    //         'short_description',
    //         'description',
    //         'is_active',
    //         'weight',
    //         'width',
    //         'height',
    //         'length',
    //         'deleted_at',
    //         'created_at',
    //         'updated_at',
    //     ]; // allowed DB columns

    //     $insertRows = [];

    //     foreach ($dataRows as $r) {
    //         // protect against ragged rows
    //         $assoc = [];
    //         foreach ($header as $i => $col) {
    //             $assoc[$col] = $r[$i] ?? null;
    //         }
    //         // whitelist and normalize
    //         $row = array_intersect_key($assoc, array_flip($allowed));
    //         $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

    //         // if (empty($row['phone'])) {
    //         //     continue; // skip if no unique identifier
    //         // }
    //         // prepare for upsert; ensure email key exists even if null
    //         $insertRows[] = [
    //             'parent_id' => ($row['parent_id']==='null'? null:$row['parent_id']),
    //             'has_variants' => (int)($row['has_variants'] ?? 0),
    //             'is_sellable' => (int)($row['is_sellable'] ?? 0),
    //             'name' =>  ucwords($row['name']),
    //             'slug' => $row['slug'] ?? null,
    //             'sku' => $row['sku'] ?? null,
    //             'barcode' => $row['barcode'] ?? null,
    //             'unit_id' => (int)($row['unit_id'] ?? null),
    //             'brand_id' => (int)($row['brand_id'] ?? null),
    //             'product_type_id' => (int)($row['product_type_id'] ?? null),
    //             'category_type_id' => (int)($row['category_type_id'] ?? null),
    //             'category_id' => (int)($row['category_id'] ?? null),
    //             'subcategory_id' => (int)($row['subcategory_id'] ?? null),
    //             'tax_id' => null,
    //             'tax_included' => (int)($row['tax_included'] ?? 0),
    //             'color_id' => (int)($row['color_id'] ?? null),
    //             'size_id' => (int)($row['size_id'] ?? null),
    //             'paper_id' => ($row['paper_id']==='null'? null:$row['paper_id']),
    //             'price' => $row['price'] ?? 0.00,
    //             'cost_price' => $row['cost_price'] ?? null,
    //             'mrp' => $row['mrp'] ?? null,
    //             'discount_type' => $row['discount_type'] ?? null,
    //             'discount_value' => $row['discount_value'] ?? null,
    //             'discount_starts_at' => $row['discount_starts_at'] ?? null,
    //             'discount_ends_at' => $row['discount_ends_at'] ?? null,
    //             'track_stock' => (int)($row['track_stock'] ?? 1),
    //             'reorder_level' => $row['reorder_level'] ?? null,
    //             'image' => $row['image'] ?? null,
    //             'thumbnail_image' => $row['thumbnail_image'] ?? null,
    //             'size_chart_image' => $row['size_chart_image'] ?? null,
    //             'material' => $row['material'] ?? null,
    //             'meta_title' => $row['meta_title'] ?? null,
    //             'meta_description' => $row['meta_description'] ?? null,
    //             'meta_keywords' => $row['meta_keywords'] ?? null,
    //             'meta_image' => $row['meta_image'] ?? null,
    //             'short_description' => $row['short_description'] ?? null,
    //             'description' => $row['description'] ?? null,
    //             'is_active' => (int)($row['is_active'] ?? 1),
    //             'weight' => ($row['weight']==='null'? null:$row['weight']),
    //             'width' => ($row['width']==='null'? null:$row['width']),
    //             'height' => ($row['height']==='null'? null:$row['height']),
    //             'length' =>($row['length']==='null'? null:$row['length']),
    //             'deleted_at' => $row['deleted_at'] ?? now(),
    //             'created_at' => $row['created_at'] ?? now(),
    //             'updated_at' => $row['updated_at'] ?? now(),
    //         ];
    //     }
    //     // dd($insertRows);
    //     if (!empty($insertRows)) {
    //         // Upsert in bulk (Laravel 8+). Unique by email (or phone)
    //         Product::upsert($insertRows, ['parent_id',
    //         'has_variants',
    //         'is_sellable',
    //         'name',
    //         'slug',
    //         'sku',
    //         'barcode',
    //         'unit_id',
    //         'brand_id',
    //         'product_type_id',
    //         'category_type_id',
    //         'category_id',
    //         'subcategory_id',
    //         'tax_id',
    //         'tax_included',
    //         'color_id',
    //         'size_id',
    //         'paper_id',
    //         'price',
    //         'cost_price',
    //         'mrp',
    //         'discount_type',
    //         'discount_value',
    //         'discount_starts_at',
    //         'discount_ends_at',
    //         'track_stock',
    //         'reorder_level',
    //         'image',
    //         'thumbnail_image',
    //         'size_chart_image',
    //         'material',
    //         'meta_title',
    //         'meta_description',
    //         'meta_keywords',
    //         'meta_image',
    //         'short_description',
    //         'description',
    //         'is_active',
    //         'weight',
    //         'width',
    //         'height',
    //         'length',
    //         'deleted_at',
    //         'created_at',
    //         'updated_at']);
    //     }

    //     return response()->json(['ok' => true, 'msg' => 'Products imported successfully']);
    // }

    public function destroy(Product $product)
    {
        // $inUse = DB::table('districts')->where('district_product_id', $product->id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This product has {$inUse} district(s). Reassign them first.",
        //     ], 422);
        // }

        // DB::table('branch_business')->where('branch_id', $branch->id)->delete();

        $imagePaths         = json_decode($product->images, true) ?: [];
        $metaImagePath      = $product->meta_image;
        $thumbnailImagePath = $product->thumbnail_image;
        $sizeImagePath      = $product->size_chart_image;

        Product::where('parent_id', $product->id)->delete();
        $product->delete();


        if (!empty($imagePaths)) {
            foreach ($imagePaths as $path) {
                if ($path && file_exists($path)) {
                    @unlink($path);
                }
            }
        }

        // Also clean up singular image if it's different (unlikely but safe)
        $singleImage = $product->image;
        if ($singleImage && !in_array($singleImage, $imagePaths) && file_exists($singleImage)) {
            @unlink($singleImage);
        }
        if (isset($metaImagePath) && file_exists($metaImagePath)) {
            unlink($metaImagePath);
        }
        if (isset($thumbnailImagePath) && file_exists($thumbnailImagePath)) {
            unlink($thumbnailImagePath);
        }
        if (isset($sizeImagePath) && file_exists($sizeImagePath)) {
            unlink($sizeImagePath);
        }

        return response()->json(['ok' => true, 'msg' => 'product deleted']);
    }
    public function select2(Request $r)
    {

        $q    = trim($r->input('q', ''));
        $base = Product::query()->where('is_active', 1);

        if ($r->boolean('sellable')) {
            $base->where('is_sellable', 1);
        }

        if ($q !== '') {
            $base->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        $items = $base->orderBy('id')->orderBy('name')
            ->limit(20)->get(['id', 'name']);

        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->name,
            ]),
        ]);
    }

    /**
     * AJAX: Parent products list (for left gallery)
     */

    public function parentsIndex(Request $r)
    {
        $query = trim($r->get('q', ''));
        $page  = (int) $r->get('page', 1);
        $limit = 8;

        $base = Product::query()
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->select('id', 'name', 'sku', 'image', 'has_variants', 'is_sellable');

        if ($query) {
            $base->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('sku', 'like', "%$query%");
            });
        }

        $total = (clone $base)->count();

        $rows = $base->orderBy('id', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get()
            ->map(function ($p) {
                return [
                    'id'           => $p->id,
                    'name'         => $p->name,
                    'sku'          => $p->sku,
                    'image'        => image($p->image),
                    'has_variants' => (bool) $p->has_variants,
                    'is_sellable'  => (bool) $p->is_sellable,
                ];
            });

        return response()->json([
            'data'      => $rows,
            'next_page' => ($total > $page * $limit),
            'page'      => $page,
        ]);
    }

    /**
     * Select2 dropdown: parent list
     */
    public function parentsSelect2(Request $r)
    {
        $term  = $r->get('q', '');
        $query = Product::query()
            ->whereNull('parent_id')

            ->where('is_active', 1)
            ->select('id', 'name', 'sku');

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                    ->orWhere('sku', 'like', "%$term%");
            });
        }

        $results = $query->orderBy('name')->limit(20)->get()->map(function ($p) {
            return [
                'id'   => $p->id,
                'text' => $p->name,
                'sku'  => $p->sku,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * AJAX: Variants under a parent
     */
    // public function variants(Product $product)
    // {
    //     // 1) Try real children
    //     $variants = Product::query()
    //         ->where('parent_id', $product->id)
    //         ->where('is_sellable', 1)
    //         ->where('is_active', 1)
    //         ->select('id', 'name', 'sku', 'image', 'cost_price as default_unit_cost')
    //         ->orderBy('id', 'asc')
    //         ->get()
    //         ->map(function ($p) {
    //             return [
    //                 'id'                => $p->id,
    //                 'name'              => $p->name,
    //                 'sku'               => $p->sku,
    //                 'image'             => image($p->image),
    //                 'default_unit_cost' => $p->default_unit_cost,
    //             ];
    //         });

    //     // 2) If no children AND the product itself is a sellable single → return itself as “variant”
    //     if ($variants->isEmpty() && $product->is_active && $product->is_sellable) {
    //         $variants = collect([[
    //             'id'                => $product->id,
    //             'name'              => $product->name,
    //             'sku'               => $product->sku,
    //             'image'             => image($product->image),
    //             'default_unit_cost' => $product->cost_price,
    //         ]]);
    //     }

    //     return response()->json(['data' => $variants]);
    // }

    /**
     * AJAX: Variants under a parent (now returns system_qty per variant)
     * Route: GET /products/{product}/variants?warehouse_id=...&branch_id=...
     */
    public function variants(Request $request, Product $product)
    {
        $warehouseId = $request->query('warehouse_id') ?? null;
        $branchId    = $request->query('branch_id') ?? 0; // default 0 if not provided

        // 1) Try real children
        $variants = Product::query()
            ->where('parent_id', $product->id)
            ->where('is_sellable', 1)
            ->where('is_active', 1)
            ->select('id', 'name', 'sku', 'image', 'cost_price as default_unit_cost')
            ->orderBy('id', 'asc')
            ->get();

        // 2) If no children AND the product itself is a sellable single → treat itself as variant
        if ($variants->isEmpty() && $product->is_active && $product->is_sellable) {
            $variants = collect([$product->only(['id', 'name', 'sku', 'image', 'cost_price'])]);
            // normalize key names
            $variants = $variants->map(function ($p) {
                return (object) [
                    'id'                => $p['id'],
                    'name'              => $p['name'],
                    'sku'               => $p['sku'],
                    'image'             => $p['image'],
                    'default_unit_cost' => $p['cost_price'],
                ];
            });
        }

        // 3) Map and attach system_qty for each variant (if warehouseId provided)
        $results = $variants->map(function ($p) use ($warehouseId, $branchId) {
            $systemQty = 0.0;
            if ($warehouseId) {
                // Option A: use Eloquent Model StockCurrent (recommended)
                try {
                    $row = \App\Models\StockCurrent::where('product_id', $p->id)
                        ->where('warehouse_id', $warehouseId)
                        ->where('branch_id', $branchId ?? 0)
                        ->first();
                    $systemQty = $row ? (float) $row->quantity : 0.0;
                } catch (\Throwable $e) {
                    // fallback to DB query if model missing
                    $systemQty = (float) \DB::table('stock_currents')
                        ->where('product_id', $p->id)
                        ->where('warehouse_id', $warehouseId)
                        ->where('branch_id', $branchId ?? 0)
                        ->value('quantity') ?? 0.0;
                }
            }

            return [
                'id'                => $p->id,
                'name'              => $p->name,
                'sku'               => $p->sku,
                'image'             => image($p->image),
                'default_unit_cost' => $p->default_unit_cost ?? 0,
                'system_qty'        => round($systemQty, 3),
            ];
        });

        return response()->json(['data' => $results]);
    }

    // Show barcode view
    public function barcodeIndex()
    {
        $products = Product::query()
            ->where('is_sellable', 1)
            ->where(function ($q) {
                $q->whereNotNull('barcode')
                    ->orWhereNotNull('sku');
            })
            ->get();

        return view('backend.modules.products.barcode', compact('products'));
    }

    // public function barcodePreview(Product $product)
    // {
    //     if (! $product->barcode) {
    //         return response()->json(['success' => false]);
    //     }

    //     $html = view('backend.modules.products.barcode_preview', compact('product'))->render();

    //     return response()->json([
    //         'success' => true,
    //         'html'    => $html,
    //     ]);
    // }

    public function barcodePreview(Product $product, Request $request)
    {
        if (! $product->is_sellable) {
            return response()->json([
                'success' => false,
                'message' => 'Only sellable products can have barcode labels.',
            ]);
        }

        if (! $this->isValidEan13($product->barcode)) {
            $product->barcode = $this->generateUniqueEan13();
            $product->save();
        }

        $qty = min(max((int) $request->qty, 1), 100);
        $barcodeValue = $product->barcode;

        if (! $barcodeValue) {
            return response()->json([
                'success' => false,
            ]);
        }

        $html = view('backend.modules.products.barcode_preview', compact(
            'product',
            'barcodeValue',
            'qty'
        ))->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    private function existingOrNewEan13(?string $barcode): string
    {
        return $this->isValidEan13($barcode)
            ? $barcode
            : $this->generateUniqueEan13();
    }

    private function generateUniqueEan13(): string
    {
        do {
            // Prefix 20 is used here for barcodes intended for this POS system.
            $base = '20';

            for ($i = 0; $i < 10; $i++) {
                $base .= (string) random_int(0, 9);
            }

            $barcode = $base . $this->calculateEan13CheckDigit($base);
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }

    private function isValidEan13(?string $barcode): bool
    {
        if (! is_string($barcode) || preg_match('/^\d{13}$/', $barcode) !== 1) {
            return false;
        }

        return $barcode[12] === $this->calculateEan13CheckDigit(substr($barcode, 0, 12));
    }

    private function calculateEan13CheckDigit(string $base): string
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $base[$i];
            $sum += $i % 2 === 0 ? $digit : $digit * 3;
        }

        return (string) ((10 - ($sum % 10)) % 10);
    }
}
