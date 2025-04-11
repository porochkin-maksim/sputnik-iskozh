<?php

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
        if (Schema::hasTable('ex_data')) {
            return;
        }

        Schema::create('ex_data', static function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->index();
            $table->unsignedBigInteger('reference_id')->index();
            $table->json('data');
            $table->timestamps();

            $table->unique(['type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ex_data');
    }
};
