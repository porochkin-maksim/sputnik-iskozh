<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasColumn('roles', 'name')) {
            Schema::table('roles', static function (Blueprint $table) {
                $table->string('name');
            });
        }

        if ( ! Schema::hasTable('roles_to_permissions')) {
            Schema::create('roles_to_permissions', static function (Blueprint $table) {
                $table->foreignId('role')->references('id')->on('roles')->cascadeOnDelete();
                $table->unsignedInteger('permission');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_to_permissions');
        if (Schema::hasColumn('roles', 'name')) {
            Schema::table('roles', static function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
