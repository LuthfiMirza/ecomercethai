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
        $connection = Schema::getConnection();
        $connectionName = $connection->getName();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $foreignKeys = DB::connection($connectionName)->select("PRAGMA foreign_key_list('$table')");
            foreach ($foreignKeys as $foreignKey) {
                if (($foreignKey->from ?? null) === 'product_id') {
                    return true;
                }
            }

            return false;
        }

        if ($driver !== 'mysql') {
            return false;
        }

        $database = $connection->getDatabaseName();

        $result = DB::connection($connectionName)->selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$database, $table, $constraint]
        );

        return $result !== null;
    }
};
