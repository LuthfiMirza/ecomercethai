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
                $table->string('payment_status')->default('pending')->after('discount_amount');
            }

            if (!Schema::hasColumn('orders', 'payment_proof_path')) {
                $table->string('payment_proof_path')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('payment_proof_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->dropColumn('payment_verified_at');
            }

            if (Schema::hasColumn('orders', 'payment_proof_path')) {
                $table->dropColumn('payment_proof_path');
            }

            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
