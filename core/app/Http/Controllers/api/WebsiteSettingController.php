<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    public function index()
    {
        $data = WebsiteSetting::latest()->first();
        return response()->json([
            'success' => true,
            'data'   => $data,
        ]);
    }
}
