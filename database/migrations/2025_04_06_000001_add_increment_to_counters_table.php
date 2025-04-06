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
        if (Schema::hasColumn('counters', 'increment')) {
            return;
        }

        Schema::table('counters', static function (Blueprint $table) {
            $table->unsignedInteger('increment')->nullable()->default(0)->after('is_invoicing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('counters', 'increment')) {
            return;
        }

        Schema::table('counters', static function (Blueprint $table) {
            $table->dropColumn('increment');
        });
    }
}; 