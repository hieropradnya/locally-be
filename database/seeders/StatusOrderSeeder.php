<?php

namespace Database\Seeders;

use App\Models\StatusOrder;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'unpaid'],
            ['id' => 2, 'name' => 'packed'],
            ['id' => 3, 'name' => 'shipped'],
            ['id' => 4, 'name' => 'completed'],
            ['id' => 0, 'name' => 'canceled'],
        ];

        foreach ($statuses as $status) {
            StatusOrder::createw(['id' => $status['id']], $status);
        }
    }
}
