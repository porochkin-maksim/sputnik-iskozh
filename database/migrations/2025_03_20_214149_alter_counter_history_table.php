<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('counter_history', 'previous_id')) {
            return;
        }

        Schema::table('counter_history', static function (Blueprint $table) {
            $table->unsignedBigInteger('previous_id')->nullable()->after('counter_id')->index();
        });
    }

    public function down(): void
    {
        if ( ! Schema::hasColumn('counter_history', 'previous_id')) {
            return;
        }

        Schema::dropColumns('counter_history', ['previous_id']);
    }
};
