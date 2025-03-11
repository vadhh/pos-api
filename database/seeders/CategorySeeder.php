<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Beverages',
            'Food',
            'Snacks',
            'Electronics',
            'Household'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'description' => "Category for {$category} items"
            ]);
        }
    }
}
