<?php

namespace App\Http\Controllers;

use App\Models\Address;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', auth('api')->id())->get();
        return response()->json(['data' => $addresses], 200);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:5',
            'city_id' => 'required|integer',
            'province_id' => 'required|integer',
        ]);

        $user = auth('api')->user();
        $validated['user_id'] = $user->id;

        // kirim pesan gagal jika sudah punya alamat
        $existingAddress = Address::where('user_id', $user->id)->first();
        if ($existingAddress) {
            return response()->json([
                'message' => 'You have created an adress record.'
            ], 400);
        }


        $address = Address::create($validated);
        $address->load('city', 'province');

        return response()->json(['data' => $address], 201);
    }


    /**
     * Display the specified address.
     */
    public function show(Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $address], 200);
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'recipient_name' => 'string|max:50',
            'phone' => 'string|max:15',
            'address' => 'string',
            'postal_code' => 'nullable|string|max:5',
            'city' => 'integer',
            'province' => 'integer',
        ]);

        $address->update($validated);

        return response()->json(['data' => $address], 200);
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
