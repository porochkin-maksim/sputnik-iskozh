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
        if ( ! Schema::hasTable('roles_to_users')) {
            Schema::create('roles_to_users', function (Blueprint $table) {
                $table->foreignId('role')->references('id')->on('roles')->cascadeOnDelete();
                $table->foreignId('user')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_to_users');
        Schema::dropIfExists('roles');
    }
};
