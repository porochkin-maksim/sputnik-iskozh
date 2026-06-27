<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('periods', 'closed_at')) {
            Schema::table('periods', static function (Blueprint $table) {
                $table->timestamp('closed_at')->nullable()->after('is_closed');
            });
        }

        if ( ! Schema::hasColumn('periods', 'closed_by')) {
            Schema::table('periods', static function (Blueprint $table) {
                $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
                $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('periods', 'closed_at')) {
            Schema::table('periods', static function (Blueprint $table) {
                $table->dropColumn('closed_at');
            });
        }

        if (Schema::hasColumn('periods', 'closed_by')) {
            Schema::table('periods', static function (Blueprint $table) {
                $table->dropForeign(['closed_by']);
                $table->dropColumn('closed_by');
            });
        }
    }
};
