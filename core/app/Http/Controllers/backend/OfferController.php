<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Product;
use App\Models\backend\Category;
use App\Models\backend\Offer;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.modules.offer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        return view('backend.modules.offer.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name'                  => 'required|string|max:255',
            'offer_type'            => 'required|in:gift,bundle,shipping,discount',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'announcement_start_at' => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
            'announcement_end_at'   => 'nullable|date|after_or_equal:announcement_start_at|before_or_equal:end_date',
        ];

        // Type specific validation
        if ($request->offer_type == 'gift') {
            $rules['buy_product_id'] = 'required|exists:products,id';
            $rules['gift_type'] = 'required|in:same_product,different_product';
            if ($request->gift_type == 'different_product') {
                $rules['free_product_id'] = 'required|exists:products,id';
            }
        } elseif ($request->offer_type == 'bundle') {
            $rules['bundle_buy_product_id'] = 'required|exists:products,id';
            $rules['bundle_buy_quantity']   = 'required|integer|min:1';
            $rules['bundle_items']          = 'required|array|min:1';
            $rules['bundle_items.*.free_product_id'] = 'required|exists:products,id';
            $rules['bundle_items.*.free_quantity']   = 'required|integer|min:1';
        } elseif ($request->offer_type == 'shipping') {
            $rules['free_shipping_on'] = 'required|in:bkash,cod,both,all_methods';
            $rules['shipping_applies_to'] = 'required|in:all_products,specific_products';
        } elseif ($request->offer_type == 'discount') {
            $rules['discount_type']  = 'required|in:percentage,fixed';
            $rules['discount_value'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = uploadImage($request->file('banner_image'), 'offers', '0', 80);
        }

        try {
            $this->offerService->createOffer($data);
            return response()->json(['message' => 'Offer created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $offerId = decrypt($id);
        $offer = Offer::with(['gift', 'giftItems', 'shipping.customAreas', 'shipping.products', 'discount.products', 'discount.categories'])->findOrFail($offerId);
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        return view('backend.modules.offer.edit', compact('offer', 'products', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $offerId = decrypt($id);
        $offer = Offer::findOrFail($offerId);

        $rules = [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'announcement_start_at' => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
            'announcement_end_at' => 'nullable|date|after_or_equal:announcement_start_at|before_or_equal:end_date',
        ];

        if ($offer->offer_type == 'gift') {
            $rules['buy_product_id'] = 'required|exists:products,id';
            $rules['gift_type'] = 'required|in:same_product,different_product';
            if ($request->gift_type == 'different_product') {
                $rules['free_product_id'] = 'required|exists:products,id';
            }
        } elseif ($offer->offer_type == 'bundle') {
            $rules['bundle_buy_product_id'] = 'required|exists:products,id';
            $rules['bundle_buy_quantity']   = 'required|integer|min:1';
            $rules['bundle_items']          = 'required|array|min:1';
            $rules['bundle_items.*.free_product_id'] = 'required|exists:products,id';
            $rules['bundle_items.*.free_quantity']   = 'required|integer|min:1';
        } elseif ($offer->offer_type == 'shipping') {
            $rules['free_shipping_on'] = 'required|in:bkash,cod,both,all_methods';
            $rules['shipping_applies_to'] = 'required|in:all_products,specific_products';
        } elseif ($offer->offer_type == 'discount') {
            $rules['discount_type']  = 'required|in:percentage,fixed';
            $rules['discount_value'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('banner_image')) {
            if ($offer->banner_image && file_exists($offer->banner_image)) {
                @unlink($offer->banner_image);
            }
            $data['banner_image'] = uploadImage($request->file('banner_image'), 'offers', '0', 80);
        }

        try {
            $this->offerService->updateOffer($offer, $data);
            return response()->json(['message' => 'Offer updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $offerId = decrypt($id);
        $offer = Offer::findOrFail($offerId);
        try {
            if ($offer->banner_image && file_exists($offer->banner_image)) {
                @unlink($offer->banner_image);
            }
            $this->offerService->deleteOffer($offer);
            return response()->json(['message' => 'Offer deleted successfully', 'success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $offer = Offer::find($request->id);
        if ($offer) {
            $offer->is_active = $request->status;
            $offer->save();
            return response()->json(['message' => 'Offer status updated successfully']);
        }
        return response()->json(['message' => 'Offer not found'], 404);
    }

    public function ajaxIndex(Request $request)
    {
        $columns = [
            'id',
            'name',
            'offer_type',
            'start_date',
            'end_date',
            'is_active',
            'created_at',
        ];

        $draw = $request->draw;
        $row = $request->start;
        $rowPerPage = $request->length;
        $columnIndex = $request->order[0]['column'];
        $columnName = $columns[$columnIndex] ?? $columns[0];
        $columnSortOrder = $request->order[0]['dir'];
        $searchValue = $request->search['value'];
        
        $query = Offer::query();

        if (!empty($searchValue)) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('offer_type', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = Offer::count();
        $totalFilteredRecords = $query->count();

        $offers = $query->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $allData = [];
        foreach ($offers as $key => $offer) {
            $checkMark = '<label class="checkboxs"><input type="checkbox" data-value="' . $offer->id . '"><span class="checkmarks"></span></label>';
            
            $status = '<span style="cursor: pointer;" class="badge changeStatus text-sm fw-semibold px-20 py-9 radius-4 text-white ' . ($offer->is_active ? 'bg-success-600' : 'bg-lilac-600') . '" data-offer-id="' . $offer->id . '">' . ($offer->is_active ? 'Active' : 'Inactive') . '</span>';

            $action = '
            <div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="' . route("offers.edit", ['offer' => encrypt($offer->id)]) . '" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-success-focus text-success-main" title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>
                <a class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main delete-btn" href="' . route("offers.destroy", ['offer' => encrypt($offer->id)]) . '" style="cursor: pointer;">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data = [];
            $data[] = $offer->id;
            $data[] = $offer->name;
            $data[] = ucfirst($offer->offer_type);
            $data[] = $offer->start_date->format('Y-m-d H:i') . ' to ' . $offer->end_date->format('Y-m-d H:i');
            $data[] = $status;
            $data[] = $offer->created_at->format('Y-m-d');
            $data[] = $action;
            $allData[] = $data;
        }

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilteredRecords,
            "aaData" => $allData,
        ];

        return response()->json($response);
    }
}
