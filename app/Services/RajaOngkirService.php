<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY');
    }

    public function provinsi()
    {
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->get('https://api.rajaongkir.com/starter/province');

        if ($response->successful()) {
            return $response['rajaongkir']['results'];
        }

        return null;
    }

    public function kotaDariProvinsi($provinceId)
    {
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->get('https://api.rajaongkir.com/starter/city', [
            'province' => $provinceId
        ]);

        if ($response->successful()) {
            return $response['rajaongkir']['results'];
        }

        return null;
    }
}
