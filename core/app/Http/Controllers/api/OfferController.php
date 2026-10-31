<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\Offer;
use App\Models\backend\OfferDiscount;
use App\Models\backend\OfferGift;
use App\Models\backend\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
       
        $query = Offer::where('is_active', 1)->where('start_date', '<=', $currentDateTime)->where('end_date', '>=', $currentDateTime);

        if($request->category){
            $query->where('slug', $request->category);
        }

        $offers = $query->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Offers retrieved successfully',
            'data' => $offers,
        ]);
    }

    public function products($slug) 
    {
        $offer = Offer::where('slug', $slug)->first();

        $products = [];

        if($offer->offer_type == 'gift') {
           $products = OfferGift::where('offer_id', $offer->id)->get()->pluck('buy_product_id')->toArray();
           $products = Product::with('category')->whereIn('id', $products)->get(); 
        } elseif($offer->offer_type == 'bundle') {
            $products = $offer->giftItems->pluck('buy_product_id')->toArray();
            $products = Product::with('category')->whereIn('id', $products)->get();
        } elseif($offer->offer_type == 'shipping') {
            $products = $offer->shipping->products->pluck('product_id')->toArray();
            $products = Product::with('category')->whereIn('id', $products)->get();
        } elseif($offer->offer_type == 'discount') {
            $products = $offer->discount->products->pluck('product_id')->toArray();
            $discountRule = OfferDiscount::where('offer_id', $offer->id)->first();
            $products = Product::with('category')->whereIn('id', $products)->get()->map(function($product) use($discountRule){
                $product->offer_price = $product->mrp - applyDiscount($discountRule, $product);
                return $product;
            }); 
        }

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }
}
