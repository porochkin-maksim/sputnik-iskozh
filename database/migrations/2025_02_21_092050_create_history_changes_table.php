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
        if (Schema::hasTable('history_changes')) {
            return;
        }
        Schema::create('history_changes', static function (Blueprint $table) {
            $table->id();
            $table->smallInteger('type', unsigned: true)->index();
            $table->smallInteger('reference_type')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('primary_id')->nullable()->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->json('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_changes');
    }
};
