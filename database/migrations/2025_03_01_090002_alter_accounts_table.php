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
        if (Schema::hasColumn('accounts', 'deleted_at')) {
            return;
        }

        Schema::table('accounts', static function (Blueprint $table) {
            $table->decimal('balance')->after('size')->default(0);
            $table->boolean('is_verified')->after('balance')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('accounts', 'deleted_at')) {
            return;
        }

        Schema::table('accounts', static function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('balance');
            $table->dropColumn('is_verified');
        });
    }
};
