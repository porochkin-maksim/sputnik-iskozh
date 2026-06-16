<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->decimal('rounding')->default(0.00)->after('debt');
        });

        echo "→ billing:migrate-transactions\n";
        Artisan::call('billing:migrate-transactions');
        echo Artisan::output();

        echo "→ billing:period:truncate --period=2 --force\n";
        Artisan::call('billing:period:truncate', ['--period' => '2', '--force' => true]);
        echo Artisan::output();

        echo "→ billing:invoices:recalc --period=1\n";
        Artisan::call('billing:invoices:recalc', ['--period' => '1', '--force' => true]);
        echo Artisan::output();
    }

    public function down(): void
    {
        Schema::table('invoices', static function (Blueprint $table) {
            $table->dropColumn('rounding');
        });
    }
};