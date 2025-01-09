<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vouchers')->insert([
            [
                'code' => 'DISCOUNT50',
                'discount_percentage' => 50,
                'expiry_date' => '2025-06-30',
                'is_active' => 1,
            ],
            [
                'code' => 'newyear25',
                'discount_percentage' => 25,
                'expiry_date' => '2025-05-15',
                'is_active' => 1,
            ],
            [
                'code' => 'summersale',
                'discount_percentage' => 15,
                'expiry_date' => '2025-07-31',
                'is_active' => 1,
            ],
            [
                'code' => 'kepaladua',
                'discount_percentage' => 20,
                'expiry_date' => '2025-01-15',
                'is_active' => 1,
            ],
            [
                'code' => 'gacor',
                'discount_percentage' => 40,
                'expiry_date' => '2025-11-29',
                'is_active' => 1,
            ],
        ]);
    }
}
