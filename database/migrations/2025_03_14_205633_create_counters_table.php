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
            Schema::create('counters', static function (Blueprint $table) {
                $table->id();
                $table->tinyInteger('type')->index();
                $table->foreignId('account_id')->references('id')->on('accounts')->restrictOnDelete();
                $table->string('number')->unique()->index();
                $table->boolean('is_invoicing')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if ( ! Schema::hasTable('counter_history')) {
            Schema::create('counter_history', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('counter_id')->nullable()->references('id')->on('counters')->restrictOnDelete();
                $table->unsignedBigInteger('value');
                $table->date('date');
                $table->boolean('is_verified')->default(false);
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
