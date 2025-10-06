<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->integer('stock')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('stock');
            }
        });

        if (Schema::hasColumn('products', 'slug')) {
            $products = DB::table('products')
                ->select('id', 'name', 'slug')
                ->orderBy('id')
                ->get();

            foreach ($products as $product) {
                if (! empty($product->slug)) {
                    continue;
                }

                $base = Str::slug($product->name) ?: 'product';
                $candidate = $base;
                $suffix = 1;

                while (
                    DB::table('products')
                        ->where('slug', $candidate)
                        ->where('id', '!=', $product->id)
                        ->exists()
                ) {
                    $candidate = $base . '-' . $suffix;
                    $suffix++;
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['slug' => $candidate]);
            }

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE products MODIFY slug VARCHAR(255) NOT NULL');
            }

            $driver = DB::getDriverName();
            $hasSlugIndex = match ($driver) {
                'mysql' => collect(DB::select("SHOW INDEX FROM products WHERE Key_name = 'products_slug_unique'"))->isNotEmpty(),
                'sqlite' => collect(DB::select("PRAGMA index_list('products')"))
                    ->contains(fn ($index) => ($index->name ?? $index->seqname ?? null) === 'products_slug_unique'),
                default => false,
            };

            if (! $hasSlugIndex) {
                Schema::table('products', function (Blueprint $table) {
                    $table->unique('slug', 'products_slug_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        $driver = DB::getDriverName();
        $hasSlugIndex = match ($driver) {
            'mysql' => collect(DB::select("SHOW INDEX FROM products WHERE Key_name = 'products_slug_unique'"))->isNotEmpty(),
            'sqlite' => collect(DB::select("PRAGMA index_list('products')"))
                ->contains(fn ($index) => ($index->name ?? $index->seqname ?? null) === 'products_slug_unique'),
            default => false,
        };

        Schema::table('products', function (Blueprint $table) use ($hasSlugIndex) {
            if ($hasSlugIndex) {
                $table->dropUnique('products_slug_unique');
            }

            if (Schema::hasColumn('products', 'slug')) {
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('products', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
