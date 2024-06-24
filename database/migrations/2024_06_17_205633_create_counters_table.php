<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable('counters')) {
            Schema::create('counters', function (Blueprint $table) {
                $table->id();
                $table->tinyInteger('type')->index();
                $table->foreignId('account_id')->references('id')->on('accounts')->restrictOnDelete();
                $table->string('number')->unique()->index();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }

        if ( ! Schema::hasTable('counter_history')) {
            Schema::create('counter_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('counter_id')->references('id')->on('counters')->restrictOnDelete();
                $table->float('value');
                $table->float('tariff');
                $table->float('cost');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_history');
        Schema::dropIfExists('counters');
    }
};
