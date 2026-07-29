<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', [
                'pending', 'processing', 'on-hold', 'completed', 
                'cancelled', 'refunded', 'failed'
            ])->default('pending');
            
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            
            $table->string('payment_method')->nullable();
            $table->string('payment_method_title')->nullable();
            $table->string('transaction_id')->nullable();
            
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            
            // Billing
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address_1')->nullable();
            $table->string('billing_address_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postcode')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();
            
            // Shipping / Installation Address
            $table->string('shipping_first_name')->nullable();
            $table->string('shipping_last_name')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_address_1')->nullable();
            $table->string('shipping_address_2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postcode')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_phone')->nullable();
            
            // Business specific
            $table->date('preferred_install_date')->nullable();
            $table->date('confirmed_install_date')->nullable();
            $table->date('removal_date')->nullable();
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('roofline_footage')->nullable();
            $table->enum('service_type', ['rental', 'purchase', 'package', 'custom'])->default('rental');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->boolean('is_guest')->default(false);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
