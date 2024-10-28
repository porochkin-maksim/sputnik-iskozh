<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (
            Schema::hasColumn('news', 'is_lock') &&
            Schema::hasColumn('news', 'category')
        ) {
            return;
        }

        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_lock')->default(false)->index()->after('id');
            $table->integer('category')->default(0)->index()->after('id');
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('news', 'is_lock')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('is_lock');
            });
        }
        if (Schema::hasColumn('news', 'category')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
