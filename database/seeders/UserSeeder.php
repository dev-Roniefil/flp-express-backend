<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Roniefil Porquiado',
            'username' => 'dev-ron',
            'email' => 'rporquiado@FLPpros.com',
            'password' => Hash::make('FLP12345678'),
            'role' => 'super_admin',
            'company' => 'FLP Express',
            'phone' => '(941) 222-1012',
        ]);

        // Add more test users if needed
        User::create([
            'name' => 'Test Customer',
            'username' => 'customer',
            'email' => 'customer@flppros.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->command->info('Users seeded successfully!');
    }
}