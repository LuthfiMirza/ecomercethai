<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'track_token')) {
                $table->string('track_token', 64)->nullable()->unique()->after('payment_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'track_token')) {
                $table->dropUnique(['track_token']);
                $table->dropColumn('track_token');
            }
        });
    }
};
