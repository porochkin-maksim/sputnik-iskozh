<?php declare(strict_types=1);

use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountExDataDTO;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\ExDataLocator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasColumn('account_to_user', 'ownerDate')) {
            Schema::table('account_to_user', function (Blueprint $table) {
                $table->date('ownerDate')->nullable()->after('fraction');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('account_to_user', 'ownerDate')) {
            return;
        }

        Schema::table('account_to_user', function (Blueprint $table) {
            $table->dropColumn('ownerDate');
        });
    }
};
