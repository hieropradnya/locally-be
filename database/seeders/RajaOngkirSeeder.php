<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;
use App\Services\RajaOngkirService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RajaOngkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    public function run()
    {
        $daftarProvinsi = $this->rajaOngkir->provinsi();

        foreach ($daftarProvinsi as $provinceRow) {
            Province::create([
                'province_id' => $provinceRow['province_id'],
                'name'        => $provinceRow['province'],
            ]);

            $daftarKota = $this->rajaOngkir->kotaDariProvinsi($provinceRow['province_id']);
            foreach ($daftarKota as $cityRow) {
                $cityNameWithType = "{$cityRow['type']} {$cityRow['city_name']}";

                City::create([
                    'province_id' => $provinceRow['province_id'],
                    'city_id'     => $cityRow['city_id'],
                    'name'        => $cityNameWithType,
                ]);
            }
        }
    }
}
