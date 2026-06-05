<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasTable('claim_to_objects')) {
            return;
        }

        $exists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'claim_to_objects')
            ->where('CONSTRAINT_NAME', 'claim_to_objects_claim_id_foreign')
            ->exists();

        if ($exists) {
            return;
        }

        Schema::table('claim_to_objects', static function (Blueprint $table): void {
            $table->foreign('claim_id', 'claim_to_objects_claim_id_foreign')
                ->references('id')
                ->on('claims')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if ( ! Schema::hasTable('claim_to_objects')) {
            return;
        }

        $exists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'claim_to_objects')
            ->where('CONSTRAINT_NAME', 'claim_to_objects_claim_id_foreign')
            ->exists();

        if ( ! $exists) {
            return;
        }

        Schema::table('claim_to_objects', static function (Blueprint $table): void {
            $table->dropForeign('claim_to_objects_claim_id_foreign');

            $table->foreign('claim_id', 'transaction_to_objects_transaction_id_foreign')
                ->references('id')
                ->on('transactions')
                ->cascadeOnDelete();
        });
    }
};
