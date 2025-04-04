<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('uids')) {
            return;
        }
        Schema::create('uids', static function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->unsignedTinyInteger('type')->index();
            $table->unsignedBigInteger('reference_id')->index();

            $table->unique(['type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uids');
    }
};
