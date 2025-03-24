<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            return;
        }

        Schema::table('users', static function (Blueprint $table) {
            $table->string('phone')->after('email')->nullable();
        });
    }

    public function down(): void
    {
        if ( ! Schema::hasColumn('users', 'phone')) {
            return;
        }

        Schema::dropColumns('users', ['phone']);
    }
};
