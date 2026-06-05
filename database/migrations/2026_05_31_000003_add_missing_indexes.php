<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists()
        ;
    }

    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table): void {
                if ( ! $this->hasIndex('payments', 'payments_invoice_id_index')) {
                    $table->index('invoice_id', 'payments_invoice_id_index');
                }
                if ( ! $this->hasIndex('payments', 'payments_account_id_index')) {
                    $table->index('account_id', 'payments_account_id_index');
                }
            });
        }

        if (Schema::hasTable('claim_to_objects')) {
            Schema::table('claim_to_objects', function (Blueprint $table): void {
                if ( ! $this->hasIndex('claim_to_objects', 'claim_to_objects_type_reference_id_index')) {
                    $table->index(['type', 'reference_id'], 'claim_to_objects_type_reference_id_index');
                }
            });
        }

        if (Schema::hasTable('acquiring')) {
            Schema::table('acquiring', function (Blueprint $table): void {
                if ( ! $this->hasIndex('acquiring', 'acquiring_invoice_id_index')) {
                    $table->index('invoice_id', 'acquiring_invoice_id_index');
                }
                if ( ! $this->hasIndex('acquiring', 'acquiring_status_index')) {
                    $table->index('status', 'acquiring_status_index');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table): void {
                if ($this->hasIndex('payments', 'payments_invoice_id_index')) {
                    $table->dropIndex('payments_invoice_id_index');
                }
                if ($this->hasIndex('payments', 'payments_account_id_index')) {
                    $table->dropIndex('payments_account_id_index');
                }
            });
        }

        if (Schema::hasTable('claim_to_objects')) {
            Schema::table('claim_to_objects', function (Blueprint $table): void {
                if ($this->hasIndex('claim_to_objects', 'claim_to_objects_type_reference_id_index')) {
                    $table->dropIndex('claim_to_objects_type_reference_id_index');
                }
            });
        }

        if (Schema::hasTable('acquiring')) {
            Schema::table('acquiring', function (Blueprint $table): void {
                if ($this->hasIndex('acquiring', 'acquiring_invoice_id_index')) {
                    $table->dropIndex('acquiring_invoice_id_index');
                }
                if ($this->hasIndex('acquiring', 'acquiring_status_index')) {
                    $table->dropIndex('acquiring_status_index');
                }
            });
        }
    }
};
