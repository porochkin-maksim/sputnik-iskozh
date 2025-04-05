<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('claims')) {
            return;
        }

        // Rename main table
        Schema::rename('transactions', 'claims');

        // Rename transaction_to_objects table and its columns
        if (Schema::hasTable('transaction_to_objects')) {
            Schema::rename('transaction_to_objects', 'claim_to_objects');
            
            // Rename the foreign key column
            Schema::table('claim_to_objects', static function ($table) {
                $table->renameColumn('transaction_id', 'claim_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            return;
        }

        // Rename claim_to_objects table and its columns back
        if (Schema::hasTable('claim_to_objects')) {
            Schema::table('claim_to_objects', static function ($table) {
                $table->renameColumn('claim_id', 'transaction_id');
            });
            Schema::rename('claim_to_objects', 'transaction_to_objects');
        }

        // Rename main table back
        Schema::rename('claims', 'transactions');
    }
}; 