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
        if (Schema::hasTable('transactions')) {
            return;
        }

        Schema::create('transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
            $table->foreignId('service_id')->references('id')->on('services')->restrictOnDelete();
            $table->string('name')->nullable();
            $table->decimal('tariff')->default(0);
            $table->decimal('cost')->default(0);
            $table->decimal('payed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
