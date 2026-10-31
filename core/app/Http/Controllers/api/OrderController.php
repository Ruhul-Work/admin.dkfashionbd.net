<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\Branch;
use App\Models\backend\Coupon;
use App\Models\backend\OfferDiscount;
use App\Models\backend\OfferGift;
use App\Models\backend\Product;
use Illuminate\Http\Request;
use App\Models\backend\Sale;
use App\Models\backend\SaleItem;
use App\Models\backend\Warehouse;
use App\Models\backend\WebsiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $orders = Sale::where('customer_id', $user->id)->orderBy('id', 'desc')->get();
            return response()->json(['success' => true, 'orders' => $orders]);
        }
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    public function show(Request $request, $invoice_no)
    {

        $order = Sale::with('items.product.color', 'items.product.size')->where('invoice_no', $invoice_no)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function create(Request $request)
    {
        //  return ($request->all());
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'coupon_id' => 'nullable|exists:coupons,id',
            'shipping_address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            // 'transaction_id' => 'required|string',

            'note' => 'nullable|string',
        ]);

        // return $data['items'];

        $subtotal = 0;

        $products = Product::whereIn('id', array_column($data['items'], 'id'))->get();
      
        $products = $products->map(function ($product) {
            $hasOffer = isProductUnderAnyOffer($product->parent_id ?? $product->id);
            //  return $hasOffer;

            if ($hasOffer && ($hasOffer->offer_type == 'gift' || $hasOffer->offer_type == 'bundle')) {
                $freeProducts = null;
                $giftProduct = OfferGift::where('offer_id', $hasOffer->id)->where('buy_product_id', $product->parent_id ?? $product->id)->get();

                // foreach($giftProduct as $gift){
                $freeProducts = Product::whereIn('id', $giftProduct->pluck('free_product_id'))->get();
                // }
                $i=0;
                foreach($freeProducts as $freeProduct){
                   
                    $freeProduct->quantity = $giftProduct[$i]['free_quantity']; 
                    $i++;
                }

                $product->freeProducts = $freeProducts;
            } else if ($hasOffer && $hasOffer->offer_type == 'discount') {
                $discountRule = OfferDiscount::where('offer_id', $hasOffer->id)->first();
                $product->offer_price = $product->mrp - applyDiscount($discountRule, $product);
            }

            // $product->hasOffer = $hasOffer;
            return $product;
        });

        //   return $products;


        $coupon = null;
        if ($data['coupon_id']) {
            $coupon = Coupon::where('id', $data['coupon_id'])->first();
        }

        $discountAmount = 0;
        $i = 0;
        foreach ($products as $product) {
            if ($product->offer_price) {
                $subtotal += $product->mrp * $data['items'][$i]['qty'];
                $discountAmount += ($product->mrp - $product->offer_price) * $data['items'][$i]['qty'];
            } else {
                $subtotal += $product->price * $data['items'][$i]['qty'];
            }
            $i++;
        }

        $coupon_discount = 0;
        if ($coupon) {
            if ($coupon->min_purchase <= $subtotal) {

                $coupon_discount = $coupon->discount;
                if ($coupon->discount_type === 'percentage') {
                    $coupon_discount = $subtotal * $coupon->discount / 100;
                }
                if ($coupon->max_discount < $coupon_discount) {
                    $coupon_discount = $coupon->max_discount;
                }
            }
        }

        $shipping_charge = WebsiteSetting::first()->shipping_charge;

        $total = $subtotal + $shipping_charge - $discountAmount - $coupon_discount;

        $ecommerceBranch = Branch::where('code', 'e-commerce')->first();
        $ecommerceWarehouse = Warehouse::where('branch_id', $ecommerceBranch->id)->first();

        DB::beginTransaction();
        try {
            $order = Sale::create([
                'invoice_no' => 'SMG-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'customer_id' => auth('sanctum')->user()->customer->id ?? null,
                'branch_id' => $ecommerceBranch->id,
                'warehouse_id' => $ecommerceWarehouse->id,
                'subtotal' => $subtotal,
                'total' => $total,
                'due_amount' => $total,
                'coupon_code' => $coupon->code ?? null,
                'coupon_id' => $coupon->id ?? null,
                'discount' => $discountAmount,
                'coupon_discount' => $coupon_discount,
                'shipping_charge' => $shipping_charge,
                'sale_type' => 'e-commerce',
                'sale_note' => $data['note'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'COD',
                // 'transaction_id' => $data['transaction_id'],
            ]);
            $i = 0;
            foreach ($data['items'] as $item) {
                // $product = Product::where('id', $item['id'])->select('id', 'name', 'price', 'thumbnail_image')->first();
                $product = $products[$i];
                if (!$product) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Product not found'], 404);
                }

                SaleItem::create([
                    'sale_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->thumbnail_image,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->offer_price ?? $product->price,
                    'line_total' => $item['qty'] * ($product->offer_price ?? $product->price)
                ]);

                if(isset($product->freeProducts)){
                    foreach ($product->freeProducts as $freeProduct) {
                        SaleItem::create([
                            'sale_id' => $order->id,
                            'product_id' => $freeProduct->id,
                            'name' => $freeProduct->name,
                            'image' => $freeProduct->thumbnail_image,
                            'quantity' => $item['qty'] * $freeProduct->quantity,
                            'unit_price' => 0.00,
                            'line_total' => 0.00,
                        ]);
                    } // end foreach freeProducts
                } // end if freeProducts
                $i++;
            } // end foreach items

            DB::commit();
            return response()->json(['success' => true, 'order' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Order not created'], 500);
        }
    }

    public function userOrders()
    {
        $orders = Sale::with('items.product')->where('customer_id', auth('sanctum')->user()->customer->id)->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'data' => $orders]);
    }
}
