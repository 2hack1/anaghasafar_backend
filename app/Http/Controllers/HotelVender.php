<?php

namespace App\Http\Controllers;

use App\Models\hotelModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HotelVender extends Controller
{
    // ✅ REGISTER VENDOR + USER
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'          => 'required',
                'email'         => 'required|email|unique:users',
                'password'      => 'required|min:6|confirmed',
                'vendor_name'   => 'required',
                'vendor_email'  => 'required|email|unique:hotel_vendors',
                'Mobilenumber'  => 'required',
                'hotelname'     => 'required',
                'hoteltype'     => 'required',
                'totalrooms'    => 'required',
                'city'          => 'required',
                'state'         => 'required',
                'pincode'       => 'required',
                'address'       => 'required',
                'baseprice'     => 'required',
                'licensefile'   => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Create User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create Vendor Record
            $vendor = hotelModel::create([
                'users_id'        => '1',
                'vendor_name'     => $request->vendor_name,
                'vendor_email'    => $request->vendor_email,
                'Mobilenumber'    => $request->Mobilenumber,
                'vendor_password' => Hash::make($request->password),
                'hotelname'       => $request->hotelname,
                'hoteltype'       => $request->hoteltype,
                'totalrooms'      => $request->totalrooms,
                'city'            => $request->city,
                'state'           => $request->state,
                'pincode'         => $request->pincode,
                'address'         => $request->address,
                'baseprice'       => $request->baseprice,
                'gstnumber'       => $request->gstnumber,
                'licensefile'     => $request->licensefile,
                'hotel_images'    => $request->hotel_images ?? [],
            ]);

            // Create Sanctum token
            // $token = $user->createToken('auth_token')->plainTextToken;
            // $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'      => 'Vendor registered successfully',
                // 'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user,
                'vendor'       => $vendor
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ✅ LOGIN VENDOR USING SANCTUM
//     public function login(Request $request)
//     {
        
//         $request->validate([
//             'vendor_email'    => 'required|vendor_email',
//             'vendor_password' => 'required',
//         ]);
// $dd = $request->all();
//         dd($dd);
//         $user = hotelModel::where('vendor_email', $request->vendor_email)->first();

//         if (!$user || !Hash::check($request->vendor_password, $user->vendor_password)) {
//             return response()->json(['error' => 'Invalid credentials'], 401);
//         }

//         // Create Sanctum Token
//         // $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'message'      => 'Login successful',
//             // 'access_token' => $token,
//             'token_type'   => 'Bearer',
//             'user'         => $user
//         ]);
//     }

public function login(Request $request)
{
    $request->validate([
        'vendor_email'    => 'required|email',
        'vendor_password' => 'required',
    ]);

    $user = hotelModel::where('vendor_email', $request->vendor_email)->first();

    if (!$user || !Hash::check($request->vendor_password, $user->vendor_password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // If you're using Sanctum, uncomment this:
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message'      => 'Login successful',
        'access_token' => $token,
        'user'         => $user
    ]);
}


    // ✅ LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // ✅ AUTHENTICATED USER INFO
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // ✅ Get All Vendors
    
    public function index()
    {
        return hotelModel::with('user')->get();
    }

    // ✅ Get Specific Vendor
    public function show($id)
    {
        $vendor = hotelModel::with('user')->find($id);
        return $vendor ?? response()->json(['error' => 'Not found'], 404);
    }

    // ✅ Add Vendor Manually (without user)
    public function store(Request $request)
    {
        $vendor = hotelModel::create($request->all());
        return response()->json($vendor);
    }

    // ✅ Update Vendor
    public function update(Request $request, $id)
    {
        $vendor = hotelModel::find($id);
        if (!$vendor) return response()->json(['error' => 'Not found'], 404);

        $vendor->update($request->all());
        return response()->json($vendor);
    }

    // ✅ Delete Vendor
    public function destroy($id)
    {
        $vendor = hotelModel::find($id);
        if (!$vendor) return response()->json(['error' => 'Not found'], 404);

        $vendor->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
