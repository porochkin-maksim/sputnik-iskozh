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
        if ( ! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
            });
        }
        if ( ! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
            });
        }
        if ( ! Schema::hasTable('roles_to_permissions')) {
            Schema::create('roles_to_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->foreignId('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_to_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
