<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('stock')
                    ->constrained()
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('brand');
            }

            if (! Schema::hasColumn('products', 'status')) {
                $table->string('status')->default('active')->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('products', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};
