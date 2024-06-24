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
        if (Schema::hasColumn('files', 'order')) {
            return;
        }

        Schema::table('files', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('related_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('files', 'order')) {
            return;
        }

        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
