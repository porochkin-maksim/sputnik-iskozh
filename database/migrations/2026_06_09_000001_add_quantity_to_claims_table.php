<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('claims', 'quantity')) {
            Schema::table('claims', static function (Blueprint $table) {
                $table->decimal('quantity', 20, 2)->nullable()->after('paid');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('claims', 'quantity')) {
            Schema::table('claims', static function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};