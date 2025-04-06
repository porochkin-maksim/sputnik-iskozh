<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasTable('options')) {
            return;
        }

        if ( ! Schema::hasColumn('options', 'data')) {
            return;
        }

        Schema::table('options', static function (Blueprint $table) {
            $table->json('data')->nullable()->change();
            $table->dropColumn('type');
        });

        DB::table('options')->truncate();
    }

    public function down(): void
    {
        if ( ! Schema::hasTable('options')) {
            return;
        }

        if ( ! Schema::hasColumn('options', 'data')) {
            return;
        }

        Schema::table('options', static function (Blueprint $table) {
            $table->text('data')->nullable()->change();
            $table->string('type')->after('id');
        });
    }
}; 