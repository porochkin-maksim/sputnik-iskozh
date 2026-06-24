<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('services')->where('type', 6)->delete();

        Schema::table('invoices', static function (Blueprint $table) {
            $table->dropColumn('advance');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->decimal('advance', 20)->after('paid')->default(0);
        });
    }
};
