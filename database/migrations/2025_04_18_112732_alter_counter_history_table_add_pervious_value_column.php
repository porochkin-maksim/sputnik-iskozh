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
        if (Schema::hasColumn('counter_history', 'previous_value')) {
            return;
        }

        Schema::table('counter_history', static function (Blueprint $table) {
            $table->float('previous_value')->after('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('counter_history', 'previous_value')) {
            return;
        }

        Schema::table('counter_history', static function (Blueprint $table) {
            $table->dropColumn('previous_value');
        });
    }
};
