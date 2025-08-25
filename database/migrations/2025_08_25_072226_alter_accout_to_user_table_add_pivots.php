<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'account_to_user')) {
            return;
        }

        Schema::table('account_to_user', function (Blueprint $table) {
            $table->float('fraction')->default(1)->after('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('users', 'account_to_user')) {
            return;
        }

        Schema::table('account_to_user', function (Blueprint $table) {
            $table->dropColumn('fraction');
        });
    }
};
