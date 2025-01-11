<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::all();
        return response()->json(['data' => $vouchers], 200);
    }

    /**
     * Store a newly created voucher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|min:5|max:50|unique:vouchers,code',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'expiry_date' => 'required|date|after:today',
        ]);

        $validated['is_active'] = 1;

        $voucher = Voucher::create($validated);

        return response()->json(['message' => 'Voucher created successfully', 'data' => $voucher], 201);
    }

    /**
     * Display the specified voucher.
     */
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        return response()->json(['data' => $voucher], 200);
    }

    /**
     * Update the specified voucher in storage.
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'string|min:5|max:50|unique:vouchers,code,' . $voucher->id,
            'discount_percentage' => 'integer|min:0|max:100',
            'expiry_date' => 'date|after:today',
            'is_active' => 'boolean',
        ]);

        $voucher->update($validated);

        return response()->json(['message' => 'Voucher updated successfully', 'data' => $voucher], 200);
    }

    /**
     * Remove the specified voucher from storage.
     */
    public function destroy($id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        $voucher->delete();

        return response()->json(['message' => 'Voucher deleted successfully'], 200);
    }
}
