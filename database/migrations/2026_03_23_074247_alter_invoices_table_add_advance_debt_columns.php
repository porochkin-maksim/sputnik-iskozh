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
        if ( ! Schema::hasColumn('invoices', 'advance')) {
            Schema::table('invoices', static function (Blueprint $table) {
                $table->decimal('advance', 20)->after('payed')->default(0);
            });
        }

        if ( ! Schema::hasColumn('invoices', 'debt')) {
            Schema::table('invoices', static function (Blueprint $table) {
                $table->decimal('debt', 20)->after('advance')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'advance')) {
            Schema::table('invoices', static function (Blueprint $table) {
                $table->dropColumn('advance');
            });
        }

        if (Schema::hasColumn('invoices', 'debt')) {
            Schema::table('invoices', static function (Blueprint $table) {
                $table->dropColumn('debt');
            });
        }
    }
};