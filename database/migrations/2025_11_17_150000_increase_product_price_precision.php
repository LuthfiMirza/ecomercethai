<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PRECISION = 20;
    private const SCALE = 2;
    private const PREVIOUS_PRECISION = 12;
    private const PREVIOUS_SCALE = 2;

    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'price')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(sprintf(
                'ALTER TABLE products MODIFY price DECIMAL(%d, %d) NOT NULL DEFAULT 0',
                self::PRECISION,
                self::SCALE
            ));

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(sprintf(
                'ALTER TABLE products ALTER COLUMN price TYPE NUMERIC(%d, %d)',
                self::PRECISION,
                self::SCALE
            ));
            DB::statement('ALTER TABLE products ALTER COLUMN price SET DEFAULT 0');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'price')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(sprintf(
                'ALTER TABLE products MODIFY price DECIMAL(%d, %d) NOT NULL DEFAULT 0',
                self::PREVIOUS_PRECISION,
                self::PREVIOUS_SCALE
            ));

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(sprintf(
                'ALTER TABLE products ALTER COLUMN price TYPE NUMERIC(%d, %d)',
                self::PREVIOUS_PRECISION,
                self::PREVIOUS_SCALE
            ));
            DB::statement('ALTER TABLE products ALTER COLUMN price SET DEFAULT 0');
        }
    }
};
