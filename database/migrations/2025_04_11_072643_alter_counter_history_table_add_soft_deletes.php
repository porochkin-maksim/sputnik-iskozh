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
        if (Schema::hasColumn('counter_history', 'deleted_at')) {
            return;
        }

        Schema::table('counter_history', static function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('counter_history', 'deleted_at')) {
            return;
        }

        Schema::table('counter_history', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
