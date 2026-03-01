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
        if (Schema::hasColumn('counters', 'expire_at')) {
            return;
        }

        Schema::table('counters', static function (Blueprint $table) {
            $table->dateTime('expire_at')->after('increment')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('counters', 'expire_at')) {
            return;
        }

        Schema::table('counters', static function (Blueprint $table) {
            $table->dropColumn('expire_at');
        });
    }
};