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
        if (! Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->boolean('is_primary')->default(false);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'colors')) {
                $table->json('colors')->nullable()->after('status');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (! Schema::hasColumn('carts', 'color')) {
                $table->string('color', 80)->nullable()->after('price');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'color')) {
                $table->string('color', 80)->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'color')) {
                $table->dropColumn('color');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'color')) {
                $table->dropColumn('color');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'colors')) {
                $table->dropColumn('colors');
            }
        });

        Schema::dropIfExists('product_images');
    }
};
