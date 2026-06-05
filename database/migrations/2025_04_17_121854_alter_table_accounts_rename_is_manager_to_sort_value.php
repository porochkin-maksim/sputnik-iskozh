<?php declare(strict_types=1);

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
        if (Schema::hasColumn('accounts', 'sort_value')) {
            return;
        }

        Schema::table('accounts', static function (Blueprint $table) {
            $table->dropColumn('is_manager');
            $table->string('sort_value')->nullable()->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('accounts', 'is_manager')) {
            return;
        }

        Schema::table('accounts', static function (Blueprint $table) {
            $table->dropColumn('sort_value');
            $table->boolean('is_manager')->default(false)->after('id');
        });
    }
};
