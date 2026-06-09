<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasColumn('services', 'period_from') || ! Schema::hasColumn('services', 'period_to')) {
            return;
        }

        DB::statement('
            UPDATE services s
            JOIN periods p ON p.id = s.period_id
            SET s.period_from = p.start_at,
                s.period_to   = p.end_at
            WHERE s.period_from IS NULL
               OR s.period_to   IS NULL
        ');
    }

    public function down(): void
    {
    }
};