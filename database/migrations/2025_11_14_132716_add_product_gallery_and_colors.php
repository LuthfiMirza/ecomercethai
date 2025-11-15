<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
                $table->string('color_key')->nullable()->index();
                $table->string('file_path');
                $table->boolean('is_primary')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('product_images', function (Blueprint $table) {
                if (! Schema::hasColumn('product_images', 'color_key')) {
                    $table->string('color_key')->nullable()->after('product_id')->index();
                }

                if (! Schema::hasColumn('product_images', 'file_path')) {
                    $table->string('file_path')->after('color_key');
                }

                if (! Schema::hasColumn('product_images', 'is_primary')) {
                    $table->boolean('is_primary')->default(false)->after('file_path');
                }

                if (! Schema::hasColumn('product_images', 'sort_order')) {
                    $table->unsignedInteger('sort_order')->default(0)->after('is_primary');
                }
            });

            if (Schema::hasColumn('product_images', 'path') && Schema::hasColumn('product_images', 'file_path')) {
                DB::table('product_images')
                    ->whereNull('file_path')
                    ->update(['file_path' => DB::raw('path')]);
            }
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

        if (Schema::hasTable('product_images')) {
            Schema::dropIfExists('product_images');
        }
    }
};
