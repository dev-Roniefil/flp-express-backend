<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => '17′ Tree',
            'description' => 'Our 17’ tall Tree has a 14’ diameter (5 yards), and is perfect for both homes and businesses. We will deliver the tree, set it up, provide extension cords and timer, and when the season is over, we will come and take it all away. All you need to do is tell us where you want it and provide access to a functioning electrical outlet.',
            'price' => 595.00,
            'stock' => 20,
            'sku' => 'TREE-17',
            'status' => 'publish',
            'category_id' => 1,
            'image_url' => 'Images/Products/17-Tree.jpg',
            'gallery' => ['Images/Products/17-Tree-2.jpg'],
            'is_active' => true,
            'has_variations' => true
        ]);
    }
}