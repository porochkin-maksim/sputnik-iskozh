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
        \App\Models\Access\Role::make()->forceFill([
            'id' => \App\Models\Access\Enum\RoleEnum::ADMIN,
        ])->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_to_permissions');

        \App\Models\Access\Permission::query()->truncate();
        \App\Models\Access\Role::query()->truncate();

        if ( ! Schema::hasTable('roles_to_permissions')) {
            Schema::create('roles_to_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->foreignId('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            });
        }
    }
};
