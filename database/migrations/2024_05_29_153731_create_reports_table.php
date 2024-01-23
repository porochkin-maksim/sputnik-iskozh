<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @see \App\Models\Report
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('category')->nullable()->index();
            $table->smallInteger('type')->nullable()->index();
            $table->string('name')->nullable();
            $table->integer('year')->nullable()->index();
            $table->dateTime('start_at')->nullable()->index();
            $table->dateTime('end_at')->nullable()->index();
            $table->decimal('money', 16)->nullable();
            $table->smallInteger('version');
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
