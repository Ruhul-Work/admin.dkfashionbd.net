<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\Brand;
use App\Models\backend\Category;
use App\Models\backend\CategoryType;
use App\Models\backend\Color;
use App\Models\backend\OfferDiscount;
use App\Models\backend\OfferGift;
use App\Models\backend\PaperQuality;
use App\Models\backend\Product;
use App\Models\backend\ProductType;
use App\Models\backend\Size;
use App\Models\backend\Subcategory;
use App\Models\backend\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')->where('is_active', 1);

        if ($request->category) {
            $products->where('category_id', Category::where('slug', $request->category)->first()->id);
        }

        if ($request->search) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->sort_by == 'price_asc') {
            $products->orderBy('price', 'asc');
        } elseif ($request->sort_by == 'price_desc') {
            $products->orderBy('price', 'desc');
        } else if ($request->sort_by == 'latest') {
            $products->orderBy('created_at', 'desc');
        } else if ($request->sort_by == 'oldest') {
            $products->orderBy('created_at', 'asc');
        } else {
            $products->orderBy('created_at', 'desc');
        }

        $products = $products->where('parent_id', null)->paginate(8);

        $products = $products->through(function ($product) {
            $hasOffer = isProductUnderAnyOffer($product->id);

            if ($hasOffer && $hasOffer->offer_type == 'gift') {
                $freeProducts = null;
                $giftProduct  = OfferGift::where('offer_id', $hasOffer->id)->where('buy_product_id', $product->id)->get();

                // foreach($giftProduct as $gift){
                $freeProducts = Product::whereIn('id', $giftProduct->pluck('free_product_id'))->get();
                // }

                $product->freeProducts = $freeProducts;
            } else if ($hasOffer && $hasOffer->offer_type == 'discount') {

                $discountRule         = OfferDiscount::where('offer_id', $hasOffer->id)->first();
                $product->offer_price = $product->mrp - applyDiscount($discountRule, $product);
            }

            // $product->hasOffer = $hasOffer;
            return $product;
        });

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function show(Request $request, $slug)
    {
        $product = Product::with(['category', 'children.size', 'children.color', 'specifications'])->where('slug', $slug)->where('is_active', 1)->first();

        // return priceAfterDiscount($product);
        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        $hasOffer = isProductUnderAnyOffer($product->id);

        if ($hasOffer && ($hasOffer->offer_type == 'gift' || $hasOffer->offer_type == 'bundle')) {
            $freeProducts = null;
            $giftProduct  = OfferGift::where('offer_id', $hasOffer->id)->where('buy_product_id', $product->id)->get();

            // foreach($giftProduct as $gift){
            $freeProducts = Product::whereIn('id', $giftProduct->pluck('free_product_id'))->get();
            // }
            $i = 0;
            foreach ($freeProducts as $freeProduct) {
                $freeProduct->quantity = $giftProduct[$i]->free_quantity;
                $i++;
            }

            if ($product->children) {
                foreach ($product->children as $child) {
                    $child->freeProducts = $freeProducts;
                }
            } else {
                $product->freeProducts = $freeProducts;
            }
        } else if ($hasOffer && $hasOffer->offer_type == 'discount') {
            $discountRule = OfferDiscount::where('offer_id', $hasOffer->id)->first();
            if ($product->children) {
                foreach ($product->children as $child) {
                    $child->offer_price = $child->mrp - applyDiscount($discountRule, $child);
                }
                $product->offer_price = $product->mrp - applyDiscount($discountRule, $product);
            } else {
                $product->offer_price = $product->mrp - applyDiscount($discountRule, $product);
            }
        }

        return response()->json(['success' => true, 'data' => $product]);
    }

    // public function syncStore(Request $request)
    // {
    //     if ($request->header('Authorization') !== 'Bearer ' . env('SYNC_API_TOKEN')) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $data = $request->validate([
    //         'name'              => ['required', 'string', 'max:150'],
    //         'sku'               => ['required', 'string', 'max:150'],
    //         'slug'              => ['nullable', 'string', 'max:150'],
    //         'barcode'           => ['nullable', 'string', 'max:150'],
    //         'parent_id'         => ['nullable', 'integer'],
    //         'brand_id'          => ['required', 'integer'],
    //         'category_id'       => ['required', 'integer'],
    //         'subcategory_id'    => ['nullable', 'integer'],
    //         'unit_id'           => ['nullable', 'integer'],
    //         'product_type_id'   => ['nullable', 'integer'],
    //         'category_type_id'  => ['nullable', 'integer'],
    //         'tax_id'            => ['nullable', 'integer'],
    //         'tax_included'      => ['nullable', 'boolean'],
    //         'color_id'          => ['nullable', 'integer'],
    //         'size_id'           => ['nullable', 'integer'],
    //         'paper_id'          => ['nullable', 'integer'],
    //         'is_active'         => ['nullable', 'integer'],
    //         'has_variant'       => ['nullable', 'integer'],
    //         'material'          => ['nullable', 'string', 'max:150'],
    //         'weight'            => ['nullable', 'numeric'],
    //         'fabric_weave'      => ['nullable', 'string', 'max:150'],
    //         'fabric_type'       => ['nullable', 'string', 'max:150'],
    //         'origin_country'    => ['nullable', 'string', 'max:150'],
    //         'care_instructions' => ['nullable', 'string', 'max:250'],
    //         'durability_rating' => ['nullable', 'string', 'max:150'],
    //         'transparency_text' => ['nullable', 'string', 'max:150'],
    //         'description'       => ['nullable', 'string', 'max:350'],
    //         'short_description' => ['nullable', 'string', 'max:250'],
    //         'meta_title'        => ['nullable', 'string', 'max:150'],
    //         'meta_description'  => ['nullable', 'string', 'max:350'],
    //         'meta_keywords'     => ['nullable', 'string', 'max:250'],

    //         // Ei charta ekhon FILE hisebe asbe, string na
    //         'meta_image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
    //         'thumbnail_image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
    //         'size_chart_image'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
    //         'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

    //         'track_stock'       => ['nullable'],
    //         'reorder_level'     => ['nullable', 'numeric'],
    //         'price'             => ['nullable', 'numeric'],
    //         'cost_price'        => ['nullable', 'numeric'],
    //         'mrp'               => ['nullable', 'numeric'],
    //         'discount_type'     => ['nullable'],
    //         'discount_value'    => ['nullable', 'numeric'],
    //         'width'             => ['nullable', 'numeric'],
    //         'height'            => ['nullable', 'numeric'],
    //         'length'            => ['nullable', 'numeric'],
    //     ]);

    //     // Image file gula receive kore, app2 er nijer uploadImage() diye save kora
    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = uploadImage($request->file('image'), 'product/images');
    //     }

    //     $thumbnailPath = null;
    //     if ($request->hasFile('thumbnail_image')) {
    //         $thumbnailPath = uploadImage($request->file('thumbnail_image'), 'product/thumbnail_images');
    //     }

    //     $sizeChartPath = null;
    //     if ($request->hasFile('size_chart_image')) {
    //         $sizeChartPath = uploadImage($request->file('size_chart_image'), 'product/size_chart_images');
    //     }

    //     $metaImagePath = null;
    //     if ($request->hasFile('meta_image')) {
    //         $metaImagePath = uploadImage($request->file('meta_image'), 'product/meta_images');
    //     }

    //     $product = DB::transaction(function () use ($data, $imagePath, $thumbnailPath, $sizeChartPath, $metaImagePath) {
    //         $payload = [
    //             'name'              => ucwords($data['name']),
    //             'slug'              => $data['slug'] ?? Str::slug($data['name'] . '-' . $data['sku']),
    //             'sku'               => $data['sku'],
    //             'barcode'           => $data['barcode'] ?? $data['sku'],
    //             'parent_id'         => $data['parent_id'] ?? null,
    //             'has_variants'      => (int) ($data['has_variant'] ?? 0),
    //             'is_sellable'       => (int) (($data['has_variant'] ?? 0) ? 0 : 1),
    //             'unit_id'           => $data['unit_id'] ?? null,
    //             'brand_id'          => $data['brand_id'],
    //             'product_type_id'   => $data['product_type_id'] ?? null,
    //             'category_type_id'  => $data['category_type_id'] ?? null,
    //             'category_id'       => $data['category_id'],
    //             'subcategory_id'    => $data['subcategory_id'] ?? null,
    //             'tax_id'            => $data['tax_id'] ?? null,
    //             'tax_included'      => (bool) ($data['tax_included'] ?? false),
    //             'color_id'          => $data['color_id'] ?? null,
    //             'size_id'           => $data['size_id'] ?? null,
    //             'paper_id'          => $data['paper_id'] ?? null,

    //             // app2 er nijer business rate, app1 theke ashbe na
    //             'price'             => 0,
    //             'cost_price'        => null,
    //             'mrp'               => null,
    //             'discount_type'     => null,
    //             'discount_value'    => null,

    //             'track_stock'       => array_key_exists('track_stock', $data) ? (bool) $data['track_stock'] : true,
    //             'reorder_level'     => $data['reorder_level'] ?? null,

    //             'image'             => $imagePath,
    //             'thumbnail_image'   => $thumbnailPath,
    //             'size_chart_image'  => $sizeChartPath,
    //             'meta_image'        => $metaImagePath,

    //             'material'          => $data['material'] ?? null,
    //             'meta_title'        => $data['meta_title'] ?? null,
    //             'meta_description'  => $data['meta_description'] ?? null,
    //             'meta_keywords'     => $data['meta_keywords'] ?? null,
    //             'short_description' => $data['short_description'] ?? null,
    //             'description'       => $data['description'] ?? null,
    //             'is_active'         => (int) ($data['is_active'] ?? 1),
    //             'weight'            => $data['weight'] ?? null,
    //             'width'             => $data['width'] ?? null,
    //             'height'            => $data['height'] ?? null,
    //             'length'            => $data['length'] ?? null,
    //         ];

    //         $product = Product::updateOrCreate(
    //             ['sku' => $data['sku']],
    //             $payload
    //         );

    //         ProductSpecification::updateOrCreate(
    //             ['product_id' => $product->id],
    //             [
    //                 'material'          => $data['material'] ?? null,
    //                 'weight_gsm'        => $data['weight'] ?? null,
    //                 'fabric_type'       => $data['fabric_type'] ?? null,
    //                 'origin_country'    => $data['origin_country'] ?? null,
    //                 'care_instructions' => $data['care_instructions'] ?? null,
    //                 'durability_rating' => $data['durability_rating'] ?? null,
    //                 'transparency_text' => $data['transparency_text'] ?? null,
    //             ]
    //         );

    //         return $product;
    //     });

    //     $product->load('specifications');

    //     return response()->json([
    //         'success' => true,
    //         'message' => $product->wasRecentlyCreated
    //             ? 'Product synced successfully'
    //             : 'Product sync updated successfully',
    //         'data'    => $product,
    //     ], $product->wasRecentlyCreated ? 201 : 200);
    // }

//     public function syncStore(Request $request)
//     {
//         if ($request->header('Authorization') !== 'Bearer ' . env('SYNC_API_TOKEN')) {
//             return response()->json(['message' => 'Unauthorized'], 401);
//         }

//         $data = $request->validate([
//             'name'               => ['required', 'string', 'max:150'],
//             'sku'                => ['required', 'string', 'max:150'],
//             'slug'               => ['nullable', 'string', 'max:150'],
//             'barcode'            => ['nullable', 'string', 'max:150'],
//             'parent_id'          => ['nullable', 'integer'],

//             'category_type_name' => ['nullable', 'string', 'max:150'],
//             'category_name'      => ['required', 'string', 'max:150'],
//             'subcategory_name'   => ['nullable', 'string', 'max:150'],
//             'product_type_name'  => ['nullable', 'string', 'max:150'],
//             'brand_name'         => ['required', 'string', 'max:150'],
//             'unit_name'          => ['nullable', 'string', 'max:150'],
//             'color_name'         => ['nullable', 'string', 'max:150'],
//             'size_name'          => ['nullable', 'string', 'max:150'],
//             'paper_name'         => ['nullable', 'string', 'max:150'],

//             'is_active'          => ['nullable', 'integer'],
//             'has_variant'        => ['nullable', 'integer'],
//             'material'           => ['nullable', 'string', 'max:150'],
//             'description'        => ['nullable', 'string', 'max:350'],
//             'short_description'  => ['nullable', 'string', 'max:250'],
//             'meta_title'         => ['nullable', 'string', 'max:150'],
//             'meta_description'   => ['nullable', 'string', 'max:350'],
//             'meta_keywords'      => ['nullable', 'string', 'max:250'],

//             'images'              => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
//             'thumbnail_image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
//             'size_chart_image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
//             'meta_image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
//         ]);

//         // === Name diye lookup / create kore ID resolve kora ===
//         $categoryTypeId = $this->resolveOrCreate(CategoryType::class, $data['category_type_name'] ?? null, 'code');
//         $categoryId     = $this->resolveOrCreate(Category::class, $data['category_name'] ?? null, 'slug');
//         $subcategoryId  = $this->resolveSubcategory($data['subcategory_name'] ?? null, $categoryId);
//         $productTypeId  = $this->resolveOrCreate(ProductType::class, $data['product_type_name'] ?? null, 'code');
//         $brandId        = $this->resolveOrCreate(Brand::class, $data['brand_name'] ?? null, 'slug');
//         $unitId         = $this->resolveOrCreate(Unit::class, $data['unit_name'] ?? null, 'code');
//         $colorId        = $this->resolveOrCreate(Color::class, $data['color_name'] ?? null, 'code');
//         $sizeId         = $this->resolveOrCreate(Size::class, $data['size_name'] ?? null, 'code');
//         $paperId        = $this->resolveOrCreate(PaperQuality::class, $data['paper_name'] ?? null, 'code');

//         $imagePath      = $request->hasFile('images')
//             ? uploadImage($request->file('images'), 'product/images') : null;
//         $thumbnailPath = $request->hasFile('thumbnail_image')
//             ? uploadImage($request->file('thumbnail_image'), 'product/thumbnail_images') : null;
//         $sizeChartPath = $request->hasFile('size_chart_image')
//             ? uploadImage($request->file('size_chart_image'), 'product/size_chart_images') : null;
//         $metaImagePath = $request->hasFile('meta_image')
//             ? uploadImage($request->file('meta_image'), 'product/meta_images') : null;

//         $product = DB::transaction(function () use (
//             $data, $categoryTypeId, $categoryId, $subcategoryId, $productTypeId,
//             $brandId, $unitId, $colorId, $sizeId, $paperId,
//             $imagePath, $thumbnailPath, $sizeChartPath, $metaImagePath
//         ) {
//             $payload = [
//                 'name'              => ucwords($data['name']),
//                 'slug'              => $data['slug'] ?? Str::slug($data['name'] . '-' . $data['sku']),
//                 'sku'               => $data['sku'],
//                 'barcode'           => $data['barcode'] ?? $data['sku'],
//                 'parent_id'         => $data['parent_id'] ?? null,
//                 'has_variants'      => (int) ($data['has_variant'] ?? 0),
//                 'is_sellable'       => (int) (($data['has_variant'] ?? 0) ? 0 : 1),

//                 'unit_id'           => $unitId,
//                 'brand_id'          => $brandId,
//                 'product_type_id'   => $productTypeId,
//                 'category_type_id'  => $categoryTypeId,
//                 'category_id'       => $categoryId,
//                 'subcategory_id'    => $subcategoryId,
//                 'color_id'          => $colorId,
//                 'size_id'           => $sizeId,
//                 'paper_id'          => $paperId,

//                 'price'             => 0,
//                 'cost_price'        => null,
//                 'mrp'               => null,
//                 'discount_type'     => null,
//                 'discount_value'    => null,

//                 'track_stock'       => true,
//                 'images'             => $imagePath,
//                 'thumbnail_image'   => $thumbnailPath,
//                 'size_chart_image'  => $sizeChartPath,
//                 'meta_image'        => $metaImagePath,
//                 'material'          => $data['material'] ?? null,
//                 'meta_title'        => $data['meta_title'] ?? null,
//                 'meta_description'  => $data['meta_description'] ?? null,
//                 'meta_keywords'     => $data['meta_keywords'] ?? null,
//                 'short_description' => $data['short_description'] ?? null,
//                 'description'       => $data['description'] ?? null,
//                 'is_active'         => (int) ($data['is_active'] ?? 1),
//             ];

//             return Product::updateOrCreate(['sku' => $data['sku']], $payload);
//         });

//         $product->load('specifications');

//         return response()->json([
//             'success' => true,
//             'message' => $product->wasRecentlyCreated ? 'Product synced successfully' : 'Product sync updated successfully',
//             'data'    => $product,
//         ], $product->wasRecentlyCreated ? 201 : 200);
//     }

// /**
//  * Name diye lookup table e khoje (case-insensitive), na pele slug soho notun toiri kore, id ferot dey.
//  */
//     /**
//      *
//      * @param string $modelClass
//      * @param string|null $name
//      * @param string $keyType 'slug' or 'code' — table e kon unique field lagbe
//      */
//     private function resolveOrCreate(string $modelClass, ?string $name, string $keyType = 'slug'): ?int
//     {
//         if (empty($name)) {
//             return null;
//         }

//         $name = trim($name);

//         $record = $modelClass::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

//         if ($record) {
//             return $record->id;
//         }

//         $payload = ['name' => $name];

//         if ($keyType === 'slug') {
//             $payload['slug'] = $this->uniqueSlug($modelClass, $name);
//         } elseif ($keyType === 'code') {
//             $payload['code'] = $this->uniqueCode($modelClass, $name);
//         }

//         $record = $modelClass::create($payload);

//         return $record->id;
//     }

//     private function resolveSubcategory(?string $name, ?int $categoryId): ?int
//     {
//         if (empty($name)) {
//             return null;
//         }

//         $name = trim($name);

//         $record = Subcategory::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

//         if ($record) {
//             return $record->id;
//         }

//         $record = Subcategory::create([
//             'name'        => $name,
//             'slug'        => $this->uniqueSlug(Subcategory::class, $name),
//             'category_id' => $categoryId,
//         ]);

//         return $record->id;
//     }

//     private function uniqueSlug(string $modelClass, string $name): string
//     {
//         $base = Str::slug($name);
//         $slug = $base;
//         $i    = 1;

//         while ($modelClass::where('slug', $slug)->exists()) {
//             $slug = $base . '-' . $i;
//             $i++;
//         }

//         return $slug;
//     }

//     private function uniqueCode(string $modelClass, string $name): string
//     {
//         $base = Str::upper(Str::slug($name, '_'));
//         $code = $base;
//         $i    = 1;

//         while ($modelClass::where('code', $code)->exists()) {
//             $code = $base . '_' . $i;
//             $i++;
//         }

//         return $code;
//     }
// }

}
