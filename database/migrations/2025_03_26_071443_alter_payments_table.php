<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('payments', 'name')) {
            Schema::table('payments', static function (Blueprint $table) {
                $table->string('name')->after('verified')->nullable();
            });
        }
        if ( ! Schema::hasColumn('payments', 'data')) {
            Schema::table('payments', static function (Blueprint $table) {
                $table->json('data')->after('comment')->nullable();
            });
        }
    }

    public function down(): void
    {
        if ( ! Schema::hasColumn('payments', 'name')) {
            Schema::dropColumns('payments', ['name']);
        }
        if ( ! Schema::hasColumn('payments', 'data')) {
            Schema::dropColumns('payments', ['data']);
        }
    }
};
