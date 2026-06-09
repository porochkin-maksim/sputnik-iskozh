<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('services', 'period_from')) {
            Schema::table('services', static function (Blueprint $table) {
                $table->dateTime('period_from')->nullable()->after('period_id');
            });
        }

        if ( ! Schema::hasColumn('services', 'period_to')) {
            Schema::table('services', static function (Blueprint $table) {
                $table->dateTime('period_to')->nullable()->after('period_from');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'period_from')) {
            Schema::table('services', static function (Blueprint $table) {
                $table->dropColumn('period_from');
            });
        }

        if (Schema::hasColumn('services', 'period_to')) {
            Schema::table('services', static function (Blueprint $table) {
                $table->dropColumn('period_to');
            });
        }
    }
};