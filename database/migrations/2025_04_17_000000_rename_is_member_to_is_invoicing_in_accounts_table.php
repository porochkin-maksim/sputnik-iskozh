<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('accounts', 'is_invoicing')) {
            return;
        }

        Schema::table('accounts', static function (Blueprint $table) {
            $table->renameColumn('is_member', 'is_invoicing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('accounts', 'is_member')) {
            return;
        }

        Schema::table('accounts', static function (Blueprint $table) {
            $table->renameColumn('is_invoicing', 'is_member');
        });
    }
}; 