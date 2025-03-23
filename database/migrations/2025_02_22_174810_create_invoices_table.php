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
        if (Schema::hasTable('invoices')) {
            return;
        }
        Schema::create('invoices', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->references('id')->on('periods')->restrictOnDelete();
            $table->foreignId('account_id')->references('id')->on('accounts')->restrictOnDelete();
            $table->smallInteger('type', unsigned: true);
            $table->decimal('cost');
            $table->decimal('payed');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
