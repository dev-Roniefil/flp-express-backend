<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing orders first
        \App\Models\Order::truncate();
        \App\Models\OrderItem::truncate();
        $users = User::all();

        // Order 1: Joy Package
        $order1 = Order::create([
            'order_number' => 'FLP-2026-00001',
            'user_id' => $users->random()->id,
            'status' => 'completed',
            'subtotal' => 1495.00,
            'total' => 1495.00,
            'payment_method' => 'stripe',
            'payment_method_title' => 'Credit Card',
            'transaction_id' => 'ch_1ABC123456',
            'billing_first_name' => 'John',
            'billing_last_name' => 'Smith',
            'billing_email' => 'john@example.com',
            'billing_phone' => '(941) 555-0123',
            'billing_address_1' => '1234 Palm Ave',
            'billing_city' => 'Sarasota',
            'billing_state' => 'FL',
            'billing_postcode' => '34234',
            'billing_country' => 'US',
            'shipping_first_name' => 'John',
            'shipping_last_name' => 'Smith',
            'shipping_address_1' => '1234 Palm Ave',
            'shipping_city' => 'Sarasota',
            'shipping_state' => 'FL',
            'shipping_postcode' => '34234',
            'shipping_country' => 'US',
            'preferred_install_date' => '2026-12-10',
            'confirmed_install_date' => '2026-12-12',
            'removal_date' => '2027-01-15',
            'package_id' => 1, // Joy Package
            'roofline_footage' => 85,
            'service_type' => 'package',
            'technician_id' => 1,
            'customer_note' => 'Please install before Christmas party on Dec 20.',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 1,
            'product_name' => 'C-9 Lights, Warm White, roofline',
            'quantity' => 1,
            'price' => 1495.00,
            'total' => 1495.00,
            'meta' => ['length' => '85ft']
        ]);

        // Order 2: Jolly Package
        $order2 = Order::create([
            'order_number' => 'FLP-2026-00002',
            'user_id' => $users->random()->id,
            'status' => 'processing',
            'subtotal' => 1995.00,
            'total' => 1995.00,
            'payment_method' => 'paypal',
            'payment_method_title' => 'PayPal',
            'billing_first_name' => 'Sarah',
            'billing_last_name' => 'Johnson',
            'billing_email' => 'sarah@example.com',
            'billing_phone' => '(941) 555-0456',
            'billing_address_1' => '5678 Ocean Blvd',
            'billing_city' => 'Venice',
            'billing_state' => 'FL',
            'billing_postcode' => '34285',
            'shipping_first_name' => 'Sarah',
            'shipping_last_name' => 'Johnson',
            'shipping_address_1' => '5678 Ocean Blvd',
            'shipping_city' => 'Venice',
            'shipping_state' => 'FL',
            'shipping_postcode' => '34285',
            'preferred_install_date' => '2026-12-15',
            'package_id' => 2, // Jolly
            'roofline_footage' => 120,
            'service_type' => 'package',
            'technician_id' => 2,
        ]);

        // Add more sample orders...
        Order::create([
            'order_number' => 'FLP-2026-00003',
            'user_id' => $users->random()->id,
            'status' => 'pending',
            'subtotal' => 2995.00,
            'total' => 2995.00,
            'payment_method' => 'cash',
            'billing_first_name' => 'Michael',
            'billing_last_name' => 'Brown',
            'billing_email' => 'michael@example.com',
            'billing_phone' => '(941) 555-0789',
            'billing_address_1' => '9012 Beach Rd',
            'billing_city' => 'Sarasota',
            'billing_state' => 'FL',
            'billing_postcode' => '34242',
            'preferred_install_date' => '2026-12-20',
            'package_id' => 3, // Merry
            'roofline_footage' => 180,
            'service_type' => 'package',
        ]);

        Order::create([
            'order_number' => 'FLP-2026-00004',
            'user_id' => $users->random()->id,
            'status' => 'completed',
            'subtotal' => 450.00,
            'total' => 450.00,
            'payment_method' => 'stripe',
            'billing_first_name' => 'Emily',
            'billing_last_name' => 'Davis',
            'billing_email' => 'emily@example.com',
            'billing_phone' => '(941) 555-1122',
            'billing_address_1' => '3456 Gulf Dr',
            'billing_city' => 'Bradenton',
            'billing_state' => 'FL',
            'billing_postcode' => '34207',
            'service_type' => 'rental',
            'roofline_footage' => 45,
        ]);

        Order::create([
            'order_number' => 'FLP-2026-00005',
            'user_id' => null, // Guest order
            'status' => 'on-hold',
            'subtotal' => 1250.00,
            'total' => 1250.00,
            'payment_method' => 'paypal',
            'billing_first_name' => 'David',
            'billing_last_name' => 'Wilson',
            'billing_email' => 'david@example.com',
            'billing_phone' => '(941) 555-3344',
            'billing_address_1' => '7890 Marina Way',
            'billing_city' => 'Sarasota',
            'billing_state' => 'FL',
            'billing_postcode' => '34231',
            'service_type' => 'custom',
            'roofline_footage' => 95,
        ]);

        $this->command->info('5 sample orders created successfully!');
    }
}