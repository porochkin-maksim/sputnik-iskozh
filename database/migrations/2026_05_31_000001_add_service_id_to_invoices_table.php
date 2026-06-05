<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasTable('invoices') || Schema::hasColumn('invoices', 'service_id')) {
            return;
        }

        Schema::table('invoices', static function (Blueprint $table): void {
            $table->foreignId('service_id')
                ->nullable()
                ->after('account_id')
                ->constrained('services')
                ->nullOnDelete();

            $table->index(['period_id', 'account_id', 'type', 'service_id'], 'invoices_period_account_type_service_index');
        });
    }

    public function down(): void
    {
        if ( ! Schema::hasTable('invoices') || ! Schema::hasColumn('invoices', 'service_id')) {
            return;
        }

        Schema::table('invoices', static function (Blueprint $table): void {
            $table->dropIndex('invoices_period_account_type_service_index');
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
