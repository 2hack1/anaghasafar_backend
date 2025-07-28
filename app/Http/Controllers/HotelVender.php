<?php

namespace App\Http\Controllers;

use App\Models\hotelModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class HotelVender extends Controller
{
    // ✅ REGISTER VENDOR + USER
    public function register(Request $request)
    {

        try {
    $validator = Validator::make($request->all(), [
        'vendor_name'     => 'required',
        'vendor_email'    => 'required|email|unique:hotel_vendors',
        'vendor_password' => 'required|min:6|confirmed',
        'Mobilenumber'    => 'required',
        'hotelname'       => 'required',
        'hoteltype'       => 'required',
        'totalrooms'      => 'required',
        'city'            => 'required',
        'state'           => 'required',
        'pincode'         => 'required',
        'address'         => 'required',
        'baseprice'       => 'required',
        'gstnumber'       => 'nullable|string|max:15',
       
        'licensefile'     => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',

        'hotel_images'    => 'required|array',
        'hotel_images.*'  => 'image|mimes:jpeg,png,jpg,webp|max:5120',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // ✅ Process License File (Single Image)
    $licensePath = null;
    if ($request->hasFile('licensefile')) {
        $licenseFile = $request->file('licensefile');
        $licenseName = Str::uuid() . '_' . $licenseFile->getClientOriginalName();
        $licenseFile->storeAs('licenses', $licenseName, 'public');
        $licensePath = 'licenses/' . $licenseName;
    }

    // ✅ Process Hotel Images (Multiple Images)
      $files = $request->file('hotel_images');
            $images = [];

            foreach ($files as $file) {
                $id = uniqid() . '_' . time();
                $filename = Str::uuid() . '_' . $file->getClientOriginalName();

                // Store in 'public/gallery', accessible via 'storage/gallery/...'
                $file->storeAs('hotel', $filename, 'public');

                $images[] = [
                    'url' => 'hotel/' . $filename,
                ];
            }

  

    // ✅ Save to DB
    $vendor = hotelModel::create([
        'users_id'        => 1,
        'vendor_name'     => $request->vendor_name,
        'vendor_email'    => $request->vendor_email,
        'Mobilenumber'    => $request->Mobilenumber,
        'vendor_password' => Hash::make($request->vendor_password),
        'hotelname'       => $request->hotelname,
        'hoteltype'       => $request->hoteltype,
        'totalrooms'      => $request->totalrooms,
        'city'            => $request->city,
        'state'           => $request->state,
        'pincode'         => $request->pincode,
        'address'         => $request->address,
        'baseprice'       => $request->baseprice,
        'gstnumber'       => $request->gstnumber,
        'licensefile'     => $licensePath,
        'hotel_images'    => $images,
        'gstnumber'      => $request->gstnumber,
    ]);

    $token = $vendor->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message'      => 'Vendor registered successfully',
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'vendor'       => $vendor
    ], 201);

} catch (Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
     
    }

   
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
