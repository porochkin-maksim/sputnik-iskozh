<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE files SET related_id = parent_id WHERE type IS NULL AND parent_id IS NOT NULL AND related_id IS NULL');

        Schema::table('files', static function ($table) {
            $table->dropColumn('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('files', static function ($table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('related_id')->index();
        });

        DB::statement('UPDATE files SET parent_id = related_id WHERE type IS NULL AND related_id IS NOT NULL');
    }
};
