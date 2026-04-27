<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('counter_history', 'deleted_at')) {
            Schema::table('counter_history', static function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }

    public function down(): void
    {
        if ( ! Schema::hasColumn('counter_history', 'deleted_at')) {
            Schema::table('counter_history', static function (Blueprint $table) {
                // Восстанавливаем колонку deleted_at при откате
                $table->softDeletes();
            });
        }
    }
};
