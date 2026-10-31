<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\CompanySetting;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    public function index()
    {
        $companySetting = CompanySetting::where('is_active', 1)->first();
        if($companySetting){
            return response()->json($companySetting);
        }
        return response()->json(['message' => 'No company setting found'], 404);
    }
}
