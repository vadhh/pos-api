<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Cola',
                'category_id' => 1,
                'price' => 1.99,
                'stock' => 100,
                'sku' => 'BEV001',
                'description' => 'Refreshing cola drink'
            ],
            [
                'name' => 'Chips',
                'category_id' => 3,
                'price' => 2.50,
                'stock' => 150,
                'sku' => 'SNK001',
                'description' => 'Crispy potato chips'
            ],
            [
                'name' => 'Sandwich',
                'category_id' => 2,
                'price' => 4.99,
                'stock' => 20,
                'sku' => 'FOOD001',
                'description' => 'Fresh sandwich'
            ],
            [
                'name' => 'USB Cable',
                'category_id' => 4,
                'price' => 9.99,
                'stock' => 50,
                'sku' => 'ELEC001',
                'description' => 'USB Type-C cable'
            ],
            [
                'name' => 'Soap',
                'category_id' => 5,
                'price' => 1.50,
                'stock' => 200,
                'sku' => 'HH001',
                'description' => 'Bath soap'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
