<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => 'Patriotic',
            'slug' => 'patriotic',
            'description' => 'American flag, red/white/blue lighting, and patriotic decorations'
        ]);

        Category::create([
            'name' => 'Roofline Lighting',
            'slug' => 'roofline',
            'description' => 'C-9 lights, roofline lighting packages'
        ]);

        Category::create([
            'name' => 'Ground & Shrub',
            'slug' => 'ground',
            'description' => 'Stake lights, ground lighting, and shrub decorations'
        ]);

        Category::create([
            'name' => 'Wreaths & Garlands',
            'slug' => 'wreath',
            'description' => 'Christmas wreaths, garlands, and bows'
        ]);

        Category::create([
            'name' => 'Tree Lighting',
            'slug' => 'tree',
            'description' => 'Pre-lit trees, 17\' trees, and tree lighting packages'
        ]);

        $this->command->info('Categories seeded successfully!');
    }
}