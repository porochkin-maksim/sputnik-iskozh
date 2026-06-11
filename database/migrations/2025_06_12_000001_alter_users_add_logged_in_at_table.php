<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'logged_in_at')) {
            return;
        }

        Schema::table('users', static function (Blueprint $table) {
            $table->timestamp('logged_in_at')->after('remember_token')->nullable();
        });
    }

    public function down(): void
    {
        if ( ! Schema::hasColumn('users', 'logged_in_at')) {
            return;
        }

        Schema::dropColumns('users', ['logged_in_at']);
    }
};
