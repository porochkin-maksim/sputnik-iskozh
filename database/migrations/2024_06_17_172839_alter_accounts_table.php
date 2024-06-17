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
        if (Schema::hasColumn('accounts', 'size')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('size')->default(0)->after('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('accounts', 'size')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
