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
        if ( ! Schema::hasTable('services')) {
            Schema::create('services', static function (Blueprint $table) {
                $table->id();
                $table->smallInteger('type', unsigned: true);
                $table->foreignId('period_id')->references('id')->on('periods')->restrictOnDelete();
                $table->string('name');
                $table->decimal('cost', 20)->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
