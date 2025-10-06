<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
        });

        if (! Schema::hasColumn('categories', 'slug')) {
            return;
        }

        $categories = DB::table('categories')
            ->select('id', 'name', 'slug')
            ->orderBy('id')
            ->get();

        foreach ($categories as $category) {
            if (! empty($category->slug)) {
                continue;
            }

            $base = Str::slug($category->name) ?: 'category';
            $candidate = $base;
            $suffix = 1;

            while (
                DB::table('categories')
                    ->where('slug', $candidate)
                    ->where('id', '!=', $category->id)
                    ->exists()
            ) {
                $candidate = $base . '-' . $suffix;
                $suffix++;
            }

            DB::table('categories')
                ->where('id', $category->id)
                ->update(['slug' => $candidate]);
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE categories MODIFY slug VARCHAR(255) NOT NULL');
        }

        $hasSlugIndex = match ($driver) {
            'mysql' => collect(DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_unique'"))->isNotEmpty(),
            'sqlite' => collect(DB::select("PRAGMA index_list('categories')"))
                ->contains(fn ($index) => ($index->name ?? $index->seqname ?? null) === 'categories_slug_unique'),
            default => false,
        };

        if (! $hasSlugIndex) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unique('slug', 'categories_slug_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        $driver = DB::getDriverName();
        $hasSlugIndex = match ($driver) {
            'mysql' => collect(DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_slug_unique'"))->isNotEmpty(),
            'sqlite' => collect(DB::select("PRAGMA index_list('categories')"))
                ->contains(fn ($index) => ($index->name ?? $index->seqname ?? null) === 'categories_slug_unique'),
            default => false,
        };

        Schema::table('categories', function (Blueprint $table) use ($hasSlugIndex) {
            if ($hasSlugIndex) {
                $table->dropUnique('categories_slug_unique');
            }

            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
