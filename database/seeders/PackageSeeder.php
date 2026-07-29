<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        Package::create([
            'name' => 'Joy',
            'slug' => 'joy',
            'price' => 999,
            'description' => 'Perfect starter package for smaller homes',
            'max_roofline_ft' => 125,
            'color' => '#166534', // Green
            'is_popular' => false,
            'features' => [
                'C-9 Lights, roofline (up to 125 ft)',
                '24" Mixed Noble Wreath',
                '12" Velvet Red Bow w/ Gold Trim',
                'Stake Lights, Warm White, 12" spacing',
                '5mm Minis, Warm White',
                'Light Bursts, Warm White'
            ]
        ]);

        Package::create([
            'name' => 'Jolly',
            'slug' => 'jolly',
            'price' => 1499,
            'description' => 'Our most popular package — great value',
            'max_roofline_ft' => 125,
            'color' => '#c2410f', // Orange
            'is_popular' => true,
            'features' => [
                'C-9 Lights, roofline (up to 125 ft)',
                '24" Mixed Noble Wreath (1)',
                '12" Velvet Red Bow (1)',
                'Stake Lights, Warm White, 12" spacing (50)',
                '5mm Minis, Warm White',
                'Light Bursts, Warm White'
            ]
        ]);

        Package::create([
            'name' => 'Merry',
            'slug' => 'merry',
            'price' => 2799,
            'description' => 'Premium package for larger properties',
            'max_roofline_ft' => 125,
            'color' => '#991b1b', // Red
            'is_popular' => false,
            'features' => [
                'C-9 Lights, roofline (up to 125 ft)',
                '24" Mixed Noble Wreath (1)',
                '12" Velvet Red Bow (1)',
                'Stake Lights, Warm White, 12" spacing (50)',
                '5mm Minis, Warm White (15)',
                'Light Bursts, Warm White (6)'
            ]
        ]);

        $this->command->info('Joy, Jolly & Merry packages created successfully!');
    }
}