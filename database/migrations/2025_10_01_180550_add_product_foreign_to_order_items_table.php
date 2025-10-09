<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->foreignKeyExists('order_items', 'order_items_product_id_foreign')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! $this->foreignKeyExists('order_items', 'order_items_product_id_foreign')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $connection = Schema::getConnection()->getName();
        $database = Schema::getConnection()->getDatabaseName();

        $result = DB::connection($connection)->selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$database, $table, $constraint]
        );

        return $result !== null;
    }
};
