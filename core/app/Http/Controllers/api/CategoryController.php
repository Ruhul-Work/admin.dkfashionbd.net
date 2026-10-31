<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', 1)->get();

        if(!$categories){
            return response()->json(['success' => false, 'message' => 'Categories not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $categories]);
    }
}
