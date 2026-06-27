<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('claims', 'original_claim_id')) {
            Schema::table('claims', static function (Blueprint $table) {
                $table->unsignedBigInteger('original_claim_id')->nullable()->after('original_service_id');
                $table->foreign('original_claim_id')->references('id')->on('claims')->nullOnDelete();
            });
        }

        DB::statement("
            UPDATE claims c
            SET original_claim_id = (
                SELECT id FROM (
                    SELECT pc.id
                    FROM claims pc
                    JOIN invoices pi ON pc.invoice_id = pi.id
                    JOIN periods pp ON pi.period_id = pp.id
                    WHERE pi.account_id = (
                        SELECT i.account_id FROM invoices i WHERE i.id = c.invoice_id
                    )
                    AND pp.end_at < (
                        SELECT p.start_at FROM periods p
                        JOIN invoices i ON i.period_id = p.id
                        WHERE i.id = c.invoice_id
                    )
                    AND pp.is_closed = 1
                    AND (pc.cost - pc.paid) != 0
                    AND pc.service_id = c.original_service_id
                    ORDER BY pp.end_at DESC
                    LIMIT 1
                ) AS tmp
            )
            WHERE c.original_service_id IS NOT NULL
            AND c.original_service_id != c.service_id
            AND c.original_claim_id IS NULL
        ");
    }

    public function down(): void
    {
        if (Schema::hasColumn('claims', 'original_claim_id')) {
            Schema::table('claims', static function (Blueprint $table) {
                $table->dropForeign(['original_claim_id']);
                $table->dropColumn('original_claim_id');
            });
        }
    }
};
