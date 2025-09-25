<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\vendorBankDetails;

class VendorBankDetailsController extends Controller
{
    /**
     * Get all vendor bank details
     */
    public function index()
    {
        $vendors = vendorBankDetails::all();
        return response()->json([
            'status' => true,
            'data' => $vendors
        ]);
    }

    /**
     * Get a single vendor bank details by ID
     */
    public function show($id)
    {
        $vendor = vendorBankDetails::find($id);
        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $vendor
        ]);
    }

    /**
     * Create new vendor bank details
     */
    public function store(Request $request)
    {
        $request->validate([
            'hotel_vendor_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bank_account' => 'nullable|string|max:50',
            'ifsc' => 'nullable|string|max:20',
            'upi_id' => 'nullable|string|max:50',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $vendor = vendorBankDetails::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Vendor bank details created successfully',
            'data' => $vendor
        ]);
    }

    /**
     * Update vendor bank details
     */
    public function update(Request $request, $id)
    {
        $vendor = vendorBankDetails::find($id);
        if (!$vendor) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bank_account' => 'nullable|string|max:50',
            'ifsc' => 'nullable|string|max:20',
            'upi_id' => 'nullable|string|max:50',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            // 'status' => 'nullable|string|max:20'
        ]);

        $vendor->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Vendor bank details updated successfully',
            'data' => $vendor
        ]);
    }

    /**
     * Delete vendor bank details
     */
    // public function destroy($id)
    // {
    //     $vendor = vendorBankDetails::find($id);
    //     if (!$vendor) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Vendor not found'
    //         ], 404);
    //     }

    //     $vendor->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Vendor bank details deleted successfully'
    //     ]);
    // }
}
