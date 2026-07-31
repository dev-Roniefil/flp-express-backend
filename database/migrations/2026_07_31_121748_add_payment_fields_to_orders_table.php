<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'ssl_txn_id')) {
                $table->string('ssl_txn_id')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('orders', 'ssl_approval_code')) {
                $table->string('ssl_approval_code')->nullable()->after('ssl_txn_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'ssl_txn_id', 'ssl_approval_code']);
        });
    }
};
