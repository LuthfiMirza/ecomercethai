<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->text('subtitle')->nullable()->after('title');
            $table->integer('sort_order')->default(0)->after('link_url');
        });

        DB::statement('UPDATE banners SET sort_order = priority');

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['placement', 'starts_at', 'ends_at', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('placement')->nullable()->after('link_url');
            $table->timestamp('starts_at')->nullable()->after('placement');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
            $table->integer('priority')->default(0)->after('ends_at');
        });

        DB::statement('UPDATE banners SET priority = sort_order');

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'sort_order']);
        });
    }
};
