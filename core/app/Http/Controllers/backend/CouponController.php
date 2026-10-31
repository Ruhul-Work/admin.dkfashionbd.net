<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\Coupon;
use App\Models\backend\CouponProduct;
use App\Models\backend\CouponUsage;
use App\Models\backend\CouponUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function index()
    {
        return view('backend.modules.coupons.index');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'title', 'code', 'coupon_type', 'discount_type', 'discount', 'min_buy', 'max_discount', 'individual_max_use', 'start_date', 'end_date', 'is_active'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Coupon::query()
            ->select(['id', 'title', 'code', 'coupon_type', 'discount_type', 'discount', 'min_buy', 'max_discount', 'individual_max_use', 'start_date', 'end_date', 'is_active']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('title', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($b->title) . '</strong>';

            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';

            $addProductsBtn = '';

            if ($b->coupon_type !== 'bill') {
                $addProductsBtn = '
                <a href="' . route('coupon.addProducts', $b->id) . '" 
                class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-success-focus text-success-main"
                data-size="lg"
                data-onload="couponIndex.onLoad"
                data-onsuccess="couponIndex.onSaved"
                title="Add">
                    <iconify-icon icon="heroicons:plus"></iconify-icon>
                 </a>';
            }

            $actions = '
                <div class="d-inline-flex justify-content-end gap-1 w-100">
                ' . $addProductsBtn . '

                <a href="' . route('coupon.edit', $b->id) . '" 
                class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-success-focus text-success-main"
                data-size="lg"
                data-onload="couponIndex.onLoad"
                data-onsuccess="couponIndex.onSaved"
                title="Edit">
                    <iconify-icon icon="lucide:edit"></iconify-icon>
                </a>

                <a href="#" 
                class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-danger-focus text-danger-main btn-coupon-delete"
                data-id="' . $b->id . '"
                data-url="' . route('coupon.destroy', $b->id) . '"
                title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
                </div>';



            $data[] = [
                $b->id,
                $nameCol,
                $b->code,
                ucfirst($b->coupon_type),
                ucfirst($b->discount_type),
                $b->discount,
                $b->min_buy,
                $b->max_discount,
                $b->individual_max_use,
                $b->start_date,
                $b->end_date,
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
        return view('backend.modules.coupons.create'); // partial only
    }

    public function store(Request $req)
    {
        // dd($req->all());
        $data = $req->validate([
            'title' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'coupon_type' => ['required', 'string', 'max:50'],
            'discount_type' => ['required', 'string', 'max:50'],
            'discount' => ['required', 'numeric'],
            'min_buy' => ['required', 'numeric'],
            'max_discount' => ['required', 'numeric'],
            'individual_max_use' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'is_active'        => ['nullable', 'integer'],

        ]);




        $coupon = Coupon::create([
            'title' => ucwords($data['title']),
            'code' => $data['code'],
            'coupon_type' => $data['coupon_type'],
            'discount_type' => $data['discount_type'],
            'discount' => $data['discount'],
            'min_buy' => $data['min_buy'],
            'max_discount' => $data['max_discount'],
            'individual_max_use' => $data['individual_max_use'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active'        => $data['is_active'] ?? 0,

        ]);

        return response()->json(['ok' => true, 'msg' => 'coupon created', 'id' => $coupon->id, 'title' => $coupon->title]);
    }

    public function edit(coupon $coupon)
    {
        return view('backend.modules.coupons.edit', compact('coupon'));
    }

    public function show(coupon $coupon)
    {

        return response()->json([
            'id'   => $coupon->id,
            'name' => $coupon->name,
            'slug' => $coupon->slug,

        ]);
    }


    public function update(Request $req, Coupon $coupon)
    {

        $data = $req->validate([
            'title' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50'],
            'coupon_type' => ['required', 'string', 'max:50'],
            'discount_type' => ['required', 'string', 'max:50'],
            'discount' => ['required', 'numeric'],
            'min_buy' => ['required', 'numeric'],
            'max_discount' => ['required', 'numeric'],
            'individual_max_use' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'is_active'        => ['nullable', 'integer'],

        ]);



        $coupon->title             = ucwords($data['title']);
        $coupon->code             = $data['code'];
        $coupon->coupon_type             = $data['coupon_type'];
        $coupon->discount_type             = $data['discount_type'];
        $coupon->discount             = $data['discount'];
        $coupon->min_buy             = $data['min_buy'];
        $coupon->max_discount             = $data['max_discount'];
        $coupon->individual_max_use             = $data['individual_max_use'];
        $coupon->start_date             = $data['start_date'];
        $coupon->end_date             = $data['end_date'];
        $coupon->is_active        = $data['is_active'];

        $coupon->save();

        return redirect()->route('coupon.index')
            ->with('success', 'coupon updated successfully!');
    }

    public function couponProducts(Coupon $coupon)
    {
        return view('backend.modules.coupons.coupon_products', compact('coupon'));
    }

    public function storeCouponProducts(Request $req, Coupon $coupon)
    {
        if ($coupon->coupon_type === 'product') {

            $data = $req->validate([
                'product_id'   => ['required', 'array'],
                'products_id.*' => ['integer'],
            ]);

            foreach ($data['product_id'] as $product) {
                CouponProduct::create([
                    'coupon_id' => $coupon->id,
                    'product_id' => $product
                ]);
            }
        } else {

            $data = $req->validate([
                'user_id'   => ['required', 'array'],
                'users_id.*' => ['integer'],
            ]);

            foreach ($data['user_id'] as $user) {
                CouponUser::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user
                ]);
            }
        }
    }

    public function allCouponAssociates(Request $request, Coupon $coupon)
    {
        $columns = [];
        if ($coupon->coupon_type === 'product') {

            $columns   = ['id', 'coupon_id', 'product_id'];
        } else {
            $columns   = ['id', 'coupon_id', 'user_id'];
        }
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = [];
        if ($coupon->coupon_type === 'product') {

            $base = CouponProduct::query()
                ->select(['id', 'coupon_id', 'product_id'])->where('coupon_id', $coupon->id);
        } else {
            $base = CouponUser::query()
                ->select(['id', 'coupon_id', 'user_id'])->where('coupon_id', $coupon->id);
        }

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('title', 'like', "%{$searchVal}%")
                    ->orWhere('code', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $b) {
            $nameCol = '<strong>' . e($coupon->title) . '</strong>';

            $active = $b->is_active
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Active</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-warning-gradient px-20 py-9 radius-4 text-white">Inactive</span>';





            $actions = '
                <div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" 
                class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center
                        bg-danger-focus text-danger-main btn-coupon-delete"
                data-id="' . $b->id . '"
                data-url="' . route('coupon.couponAssociates.destroy', [
                'pivot' => $b->id,
                'type' => $coupon->coupon_type
            ]) . '"
                title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
                </div>';

            $data[] = [
                $b->id,
                $nameCol,
                $coupon->coupon_type === 'product' ? $b->product->name : $b->customer?->name,
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

    public function importCsvModal()
    {
        return view('backend.modules.coupons.import_csv');
    }

    public function importCsv(Request $req)
    {

        $sheets[] = $req->all();
        if (empty($sheets) || empty($sheets[0])) {
            return back()->with('error', 'Uploaded file is empty or unreadable.');
        }

        $rows = $sheets[0];

        // If first row is header, normalize it and map rows to assoc arrays
        $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $allowed = ['name', 'slug', 'email', 'birth_date', 'phone', 'alternate_phone', 'address', 'postal_code', 'image', 'is_active']; // allowed DB columns

        $insertRows = [];

        foreach ($dataRows as $r) {
            // protect against ragged rows
            $assoc = [];
            foreach ($header as $i => $col) {
                $assoc[$col] = $r[$i] ?? null;
            }
            // whitelist and normalize
            $row = array_intersect_key($assoc, array_flip($allowed));
            $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

            if (empty($row['phone'])) {
                continue; // skip if no unique identifier
            }
            // prepare for upsert; ensure email key exists even if null
            $insertRows[] = [
                'name' => ucwords($row['name']) ?? null,
                'slug' => $row['slug'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'alternate_phone' => $row['alternate_phone'] ?? null,
                'address' => ucwords($row['address']) ?? null,
                'postal_code' => $row['postal_code'] ?? null,
                'birth_date' => $row['birth_date'] ?? null,
                'image' => $row['image'] ?? null,
                'is_active' => (int)($row['is_active']) ?? 1
            ];
        }

        if (!empty($insertRows)) {
            // Upsert in bulk (Laravel 8+). Unique by email (or phone)
            coupon::upsert($insertRows, ['name', 'slug', 'phone', 'address', 'postal_code', 'alternate_phone', 'birth_date', 'image', 'is_active']);
        }

        return response()->json(['ok' => true, 'msg' => 'coupons imported successfully']);
    }

    public function destroy(Coupon $coupon)
    {
        // $inUse = DB::table('coupons')->where('coupon_id', $coupon->id)->count();
        // if ($inUse > 0) {
        //     return response()->json([
        //         'ok'  => false,
        //         'msg' => "This coupon has {$inUse} subcategorie(s). Reassign them first.",
        //     ], 422);
        // }

        $image      = $coupon->image;

        $coupon->delete();

        if (isset($image) && file_exists($image)) {
            unlink($image);
        }


        return response()->json(['ok' => true, 'msg' => 'coupon deleted']);
    }

    public function couponAssociatesDestroy($pivot, $type)
    {
        if ($type === 'product') {
            CouponProduct::where('id', $pivot)->delete();
        } else {
            CouponUser::where('id', $pivot)->delete();
        }



        return response()->json(['ok' => true, 'msg' => 'product removed from coupon']);
    }

    // public function getCouponById(Coupon $coupon)
    // {
    //     if ($coupon) {
    //         return response()->json(['ok' => true, 'coupon' => $coupon]);
    //     } else {
    //         return response()->json(['ok' => false, 'msg' => 'Coupon not found']);
    //     }
    // }

    // public function allCouponByUser($userId)
    // {
    //     $coupon_user = CouponUser::where('user_id', $userId)->get();

    //     if ($coupon_user->isNotEmpty()) {
    //         return response()->json(['ok' => true, 'coupons' => $coupon_user]);
    //     } else {
    //         return response()->json(['ok' => false, 'msg' => 'User not found']);
    //     }
    // }

    // public function productsByCouponId($couponId)
    // {
    //     $products = CouponProduct::where('coupon_id', $couponId)->get();

    //     if ($products) {
    //         return response()->json([
    //             'ok' => true,
    //             'products' => $products
    //         ]);
    //     } else {
    //         return response()->json(['ok' => false, 'msg' => 'Products not found']);
    //     }
    // }

    public function select2(Request $r)
    {

        $q = trim($r->input('q', ''));
        $type = $r->type;
        $base = Coupon::query()->where('is_active', 1)->where('end_date', '>=', now());


        if ($q !== '') {
            $base->where(function ($x) use ($q) {
                $x->where('code', 'like', "%{$q}%");
            });
        }

      

        $items = $base->orderBy('id', 'desc')
            ->limit(20)->get(['id', 'code']);


        return response()->json([
            'results' => $items->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->code

            ])
        ]);
    }

    private function calculateDiscount(
        float $baseAmount,
        float $discountValue,
        string $discountType,
        ?float $maxDiscount
    ): float {

        if ($discountType === 'flat') {
            return min($discountValue, $baseAmount);
        }

        // percentage
        $calculated = ($baseAmount * $discountValue) / 100;

        if ($maxDiscount !== null) {
            return min($calculated, $maxDiscount);
        }

        return $calculated;
    }


    public function preview(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'user_id'   => 'nullable|exists:customers,id',
            'cart'      => 'required|array',
            'subtotal'  => 'required|numeric|min:0',
        ]);

        $coupon   = Coupon::findOrFail($request->coupon_id);
        $userId   = $request->user_id;
        $cart     = $request->cart;
        $subtotal = (float) $request->subtotal;

        $used = CouponUsage::where('coupon_id', $coupon)->where('user_id', $userId)->count();



        // basic coupon validity
        if ($coupon->min_buy > $subtotal) {
            return response()->json([
                'eligible' => false,
                'discount' => 0,
                'message'  => "Minimum purchase {$coupon->min_buy} required"
            ]);
        }

        $discount = 0;
        if (!$userId || ($used < $coupon->individual_max_use)) {
            /* -----------------------------
       BILL BASED COUPON
    ------------------------------*/
            if ($coupon->coupon_type === 'bill') {

                $discount = $this->calculateDiscount(
                    $subtotal,
                    $coupon->discount,
                    $coupon->discount_type,
                    $coupon->max_discount
                );
            }

            /* -----------------------------
       USER BASED COUPON
    ------------------------------*/ elseif ($coupon->coupon_type === 'user') {

                if (!$userId) {
                    return response()->json([
                        'eligible' => false,
                        'discount' => 0,
                        'message'  => 'Customer selection required'
                    ]);
                }

                $eligible = CouponUser::where('coupon_id', $coupon->id)
                    ->where('user_id', $userId)
                    ->exists();

                if (!$eligible) {
                    return response()->json([
                        'eligible' => false,
                        'discount' => 0,
                        'message'  => 'User not eligible for this coupon'
                    ]);
                }

                $discount = $this->calculateDiscount(
                    $subtotal,
                    $coupon->discount,
                    $coupon->discount_type,
                    $coupon->max_discount
                );
            }

            /* -----------------------------
       PRODUCT BASED COUPON
    ------------------------------*/ else {

                $couponProducts = CouponProduct::where('coupon_id', $coupon->id)
                    ->pluck('product_id')
                    ->toArray();

                foreach ($cart as $item) {
                    if (
                        in_array($item['id'], $couponProducts) ||
                        (!empty($item['parent_id']) && in_array($item['parent_id'], $couponProducts))
                    ) {

                        $itemTotal = $item['price'] * $item['quantity'];

                        $itemDiscount = $this->calculateDiscount(
                            $itemTotal,
                            $coupon->discount,
                            $coupon->discount_type,
                            $coupon->max_discount
                        );

                        $discount += $itemDiscount;
                    }
                }
            }


            // safety cap
            $discount = min($discount, $subtotal);

            return response()->json([
                'eligible' => true,
                'discount' => round($discount, 2),
                'message'  => 'Coupon applied successfully'
            ]);
        } else {
            return response()->json([
                'eligible' => false,
                'discount' => 0,
                'message'  => 'Maximum usage limit exceeded for this user!'
            ]);
        }
    }
}
