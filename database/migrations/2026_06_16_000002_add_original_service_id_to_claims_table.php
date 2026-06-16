<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', static function (Blueprint $table) {
            $table->unsignedBigInteger('original_service_id')->nullable()->after('service_id');
            $table->foreign('original_service_id')->references('id')->on('services')->onDelete('set null');
            $table->index('original_service_id');
        });

        // Set original_service_id = service_id for existing claims
        DB::statement('UPDATE claims SET original_service_id = service_id WHERE original_service_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('claims', static function (Blueprint $table) {
            $table->dropForeign(['original_service_id']);
            $table->dropIndex(['original_service_id']);
            $table->dropColumn('original_service_id');
        });
    }
};