<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\backend\Coupon;

class CouponController extends Controller
{
    public function getCouponByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);
        
        $coupon = Coupon::where('code', $request->code)->first();
        
        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not found',
            ]);
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Coupon found',
            'data' => $coupon,
        ]);
    }
}
