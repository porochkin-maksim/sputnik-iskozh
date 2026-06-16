<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::update("
            UPDATE `payments`
            SET `account_id` = (
                SELECT `account_id` FROM `invoices` WHERE `invoices`.`id` = `payments`.`invoice_id`
            )
            WHERE `account_id` IS NULL AND `invoice_id` IS NOT NULL
        ");
    }

    public function down(): void
    {
    }
};
