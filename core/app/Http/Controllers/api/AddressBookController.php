<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\AddressBook;
use Illuminate\Http\Request;

class AddressBookController extends Controller
{
    public function index(Request $request)
    {
        
       $addresses = AddressBook::where('user_id', auth('sanctum')->user()->id)->get();
       
       if($addresses->count() > 0){
            return response()->json([
                'message' => 'No addresses found',
                'success' => false
            ]); 
       }

       return response()->json([
        'addresses' => $addresses,
        'success' => true
       ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => auth('sanctum')->user()->id,
            'name' => 'required',
            'shipping_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postal_code' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'is_primary' => 'required'
        ]);

        $address = AddressBook::create($data);

        return response()->json([
            'message' => 'Address added successfully',
            'success' => true
        ]); 
    }
}
