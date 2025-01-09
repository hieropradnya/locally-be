<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OngkirController extends Controller
{

    public function getprovince()
    {
        $daftarProvinsi = Province::select('id', 'name')->get();
        return response()->json(['data' => $daftarProvinsi]);
    }

    public function getcity()
    {
        $daftarKota = City::select('province_id', 'city_id', 'name')->get();
        return response()->json(['data' => $daftarKota]);
    }

    public function checkshipping()
    {
        // Kirim request ke Raja Ongkir API
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->post('https://api.rajaongkir.com/starter/cost', [
            'origin' => 501,
            'destination' => 114,
            'weight' => 1700,
            'courier' => 'jne',
        ]);

        if ($response->successful()) {
            return response()->json(['data' => $response->json()['rajaongkir']['results'][0]['costs'][1]['cost'][0]['value']]);
        }

        return response()->json([
            'error' => 'Failed to fetch shipping cost.',
            'status' => $response->status(),
            'message' => $response->body(),
        ], $response->status());
    }
}
