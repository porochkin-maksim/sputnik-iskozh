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
        if (Schema::hasColumn('periods', 'is_closed')) {
            return;
        }

        Schema::table('periods', static function (Blueprint $table) {
            $table->boolean('is_closed')->after('name')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('periods', 'is_closed')) {
            return;
        }

        Schema::table('periods', static function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });
    }
};
