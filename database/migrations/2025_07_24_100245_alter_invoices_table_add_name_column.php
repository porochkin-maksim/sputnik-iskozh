<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('invoices', 'name')) {
            return;
        }

        Schema::table('invoices', static function (Blueprint $table) {
            $table->string('name')->nullable()->after('payed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('invoices', 'name')) {
            return;
        }

        Schema::table('name', static function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
