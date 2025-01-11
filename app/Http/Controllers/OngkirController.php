<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OngkirController extends Controller
{

    public function getprovince()
    {
        $daftarProvinsi = Province::select('id', 'name')->get();
        return response()->json(['data' => $daftarProvinsi]);
    }

    public function getcity($id)
    {
        $daftarKota = City::select('province_id', 'id', 'name')->where('province_id', $id)->get();
        return response()->json(['data' => $daftarKota]);
    }

    public function checkshipping(Request $request, $seller_id)
    {
        try {
            $userCity = Address::where('user_id', $request->user()->id)->firstOrFail()->city_id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'You do not have delivery address'
            ], 400);
        }

        try {
            $sellerCity = Address::where('user_id', $seller_id)->firstOrFail()->city_id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Seller does not have a delivery address'
            ], 400);
        }

        // Kirim request ke Raja Ongkir API
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->post('https://api.rajaongkir.com/starter/cost', [
            'origin' => $sellerCity,
            'destination' => $userCity,
            'weight' => 1000,
            'courier' => 'jne',
        ]);

        if ($response->successful()) {
            return response()->json(['data' => $response->json()['rajaongkir']['results'][0]['costs'][1]['cost']]);
        }

        return response()->json([
            'error' => 'Failed to fetch shipping cost.',
            'status' => $response->status(),
            'message' => $response->body(),
        ], $response->status());
    }
}
