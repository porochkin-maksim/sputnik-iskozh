<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('claims', 'quantity')) {
            return;
        }

        DB::statement('
            UPDATE `claims` `c`
            JOIN `services` `s` ON `s`.`id` = `c`.`service_id`
            SET `c`.`quantity` = `c`.`cost` / `c`.`tariff`
            WHERE `c`.`tariff` > 0
              AND `c`.`quantity` IS NULL
        ');
    }

    public function down(): void
    {
    }
};