<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const array TABLES = [
        'services'  => ['cost'],
        'invoices'  => ['cost', 'paid', 'advance', 'debt'],
        'claims'    => ['tariff', 'cost', 'paid'],
        'payments'  => ['cost'],
        'acquiring' => ['amount'],
        'accounts'  => ['balance'],
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` decimal(20, 2) NOT NULL DEFAULT 0");
            }
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` decimal(20, 0) NOT NULL DEFAULT 0");
            }
        }
    }
};
