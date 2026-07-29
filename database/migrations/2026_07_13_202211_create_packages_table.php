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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Joy, Jolly, Merry
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->integer('max_roofline_ft');
            $table->json('features');                  // List of items
            $table->boolean('is_popular')->default(false);
            $table->string('color');                   // Green, Orange, Red
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
