<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Clothing',
            'Footwear',
            'Accessories',
            'Jewelry',
            'Ethnic Wear',
            'Streetwear',
            'Sportswear',
            'Formal Wear',
            'Traditional Wear',
            'Kid\'s Fashion',
            'Teen Fashion',
            'Unisex Fashion',
            'Vintage Fashion',
            'Luxury Fashion',
            'Eco-Friendly Fashion',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
